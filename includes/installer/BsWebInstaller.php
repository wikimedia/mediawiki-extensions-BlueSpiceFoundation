<?php
/**
 * Core installer web interface.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 * @ingroup Deployment
 */

/**
 * Class for the core and BlueSpice installer web interface.
 *
 * @ingroup Deployment
 * @since 2.23
 *
 * @author Stephan Muggli <muggli@hallowelt.com>
 */
class BsWebInstaller extends WebInstaller {

	public function __construct( \WebRequest $request ) {
		 // BlueSpice
		global $wgMessagesDirs;
		$wgMessagesDirs['BlueSpiceInstaller'] = dirname( dirname( __DIR__ ) ) . '/i18n/installer';

		parent::__construct( $request );
		$this->output = new BsWebInstallerOutput( $this );
	}

	/**
	 * Finds extensions that follow the format /extensions/Name/Name.php,
	 * and returns an array containing the value for 'Name' for each found extension.
	 *
	 * @return array
	 */
	public function findExtensions() {
		$aList = parent::findExtensions();
		$aExts = array();
		foreach ( $aList as $sEntry ) {
			if ( stripos( $sEntry, 'BlueSpice' ) === false ) {
				$aExts[] = $sEntry;
			}
		}

		return $aExts;
	}

	/**
	 * Installs the auto-detected extensions.
	 *
	 * @return Status
	 */
	protected function includeExtensions() {
		global $IP;
		$exts = $this->getVar( '_Extensions' );
		$IP = $this->getVar( 'IP' );

		/**
		 * We need to include DefaultSettings before including extensions to avoid
		 * warnings about unset variables. However, the only thing we really
		 * want here is $wgHooks['LoadExtensionSchemaUpdates']. This won't work
		 * if the extension has hidden hook registration in $wgExtensionFunctions,
		 * but we're not opening that can of worms
		 * @see https://bugzilla.wikimedia.org/show_bug.cgi?id=26857
		 */
		global $wgAutoloadClasses;
		$wgAutoloadClasses = array();

		require "$IP/includes/DefaultSettings.php";

		foreach ( $exts as $e ) {
			require_once "$IP/extensions/$e/$e.php";
		}

		 // BlueSpice
		require_once "$IP/extensions/BlueSpiceFoundation/BlueSpiceFoundation.php";
		require_once "$IP/extensions/BlueSpiceExtensions/BlueSpiceExtensions.php";
		require_once "$IP/extensions/BlueSpiceDistribution/BlueSpiceDistribution.php";
		require_once "$IP/skins/BlueSpiceSkin/BlueSpiceSkin.php";

		$hooksWeWant = isset( $wgHooks['LoadExtensionSchemaUpdates'] ) ?
			$wgHooks['LoadExtensionSchemaUpdates'] : array();

		// Unset everyone else's hooks. Lord knows what someone might be doing
		// in ParserFirstCallInit (see bug 27171)
		$GLOBALS['wgHooks'] = array( 'LoadExtensionSchemaUpdates' => $hooksWeWant );

		return Status::newGood();
	}

	/**
	 * Get an array of install steps. Should always be in the format of
	 * array(
	 *   'name'     => 'someuniquename',
	 *   'callback' => array( $obj, 'method' ),
	 * )
	 * There must be a config-install-$name message defined per step, which will
	 * be shown on install.
	 *
	 * @param $installer DatabaseInstaller so we can make callbacks
	 * @return array
	 */
	protected function getInstallSteps( DatabaseInstaller $installer ) {
		$coreInstallSteps = array(
			array( 'name' => 'database', 'callback' => array( $installer, 'setupDatabase' ) ),
			array( 'name' => 'tables', 'callback' => array( $installer, 'createTables' ) ),
			array( 'name' => 'interwiki', 'callback' => array( $installer, 'populateInterwikiTable' ) ),
			array( 'name' => 'stats', 'callback' => array( $this, 'populateSiteStats' ) ),
			array( 'name' => 'keys', 'callback' => array( $this, 'generateKeys' ) ),
			array( 'name' => 'sysop', 'callback' => array( $this, 'createSysop' ) ),
			array( 'name' => 'mainpage', 'callback' => array( $this, 'createMainpage' ) )
		);

		// Build the array of install steps starting from the core install list,
		// then adding any callbacks that wanted to attach after a given step
		foreach ( $coreInstallSteps as $step ) {
			$this->installSteps[] = $step;
			if ( isset( $this->extraInstallSteps[$step['name']] ) ) {
				$this->installSteps = array_merge(
					$this->installSteps,
					$this->extraInstallSteps[$step['name']]
				);
			}
		}

		// Prepend any steps that want to be at the beginning
		if ( isset( $this->extraInstallSteps['BEGINNING'] ) ) {
			$this->installSteps = array_merge(
				$this->extraInstallSteps['BEGINNING'],
				$this->installSteps
			);
		}

		// BlueSpice
		// Extensions should always go first, chance to tie into hooks and such
		array_unshift( $this->installSteps,
			array( 'name' => 'extensions', 'callback' => array( $this, 'includeExtensions' ) )
		);
		$this->installSteps[] = array(
			'name' => 'extension-tables',
			'callback' => array( $installer, 'createExtensionTables' )
		);

		return $this->installSteps;
	}

}
