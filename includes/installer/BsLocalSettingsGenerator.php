<?php
/**
 * Generator for LocalSettings.php file.
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
 * Class for generating BlueSpice LocalSettings.php file.
 *
 * @ingroup Deployment
 * @since 2.23
 *
 * @author Stephan Muggli <muggli@hallowelt.biz>
 */
class BsLocalSettingsGenerator extends LocalSettingsGenerator {

	/**
	 * Return the full text of the generated LocalSettings.php file,
	 * including the extensions
	 *
	 * @return String
	 */
	public function getText() {
		$localSettings = $this->getDefaultText();

		if ( count( $this->extensions ) ) {
			$localSettings .= "
# Enabled Extensions. Most extensions are enabled by including the base extension file here
# but check specific extension documentation for more details
# The following extensions were automatically enabled:\n";

			foreach ( $this->extensions as $extName ) {
				$encExtName = self::escapePhpString( $extName );
				$localSettings .= "require_once \"\$IP/extensions/$encExtName/$encExtName.php\";\n";
			}
		}

		// BlueSpice
		$localSettings .= "require_once \"\$IP/extensions/BlueSpiceFoundation/BlueSpiceFoundation.php\";\n";
		$localSettings .= "require_once \"\$IP/extensions/BlueSpiceExtensions/BlueSpiceExtensions.php\";\n";
		$localSettings .= "require_once \"\$IP/extensions/BlueSpiceDistribution/BlueSpiceDistribution.php\";\n";
		$localSettings .= "require_once \"\$IP/skins/BlueSpiceSkin/BlueSpiceSkin.php\";\n";

		$localSettings .= "\n\n# End of automatically generated settings.
# Add more configuration options below.\n\n";

		return $localSettings;
	}

}
