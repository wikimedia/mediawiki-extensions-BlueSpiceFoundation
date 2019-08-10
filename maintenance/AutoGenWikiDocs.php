<?php

/**
 * @copyright Copyright (C) 2016 Hallo Welt! GmbH
 * @author Robert Vogel <vogel@hallowelt.com>
 */

use UtfNormal\Validator;

// HINT: https://www.mediawiki.org/wiki/Manual:Writing_maintenance_scripts
require_once 'BSMaintenance.php';

class AutoGenWikiDocs extends BSMaintenance {

	private $file = 'AutoGenWikiDocs.xml';

	private $lang = 'en';

	private $admin = false;

	private $labeledSection = false;

	private $jsonFiles = [];

	private $data = [];

	private $pages = [];

	private $permissions = [];

	private $roles = [];

	public function __construct() {
		parent::__construct();

		$this->addOption( 'file', 'Filename for output XML', false, true );
		$this->addOption( 'lang', 'The language', false, true );
		$this->addOption( 'admin', 'Create extended documentation', false, false );
		$this->addOption( 'label', 'Use labeled sections (for use with Extension:LabeledSectionTransclusion)', false, false );
	}

	public function execute() {
		$this->lang = $this->getOption( 'lang', 'en' );
		$this->file = $this->getOption( 'file', 'AutoGenWikiDocs.xml' );
		$this->admin = $this->getOption( 'admin', false );
		$this->labeledSection = $this->getOption( 'label', false );

		$this->output( "Starting... ({$this->lang})\n" );

		$this->readInExtensionJSONFiles();
		$this->readInSkinJSONFiles();

		$this->buildDocs();
		$this->buildRolesTable();
		$this->buildPermissionsTable();

		$xml = $this->createExportXml();
		$this->createOutputFile( $xml );
	}

	private function readInExtensionJSONFiles() {
		$this->readInJSONFile( 'extensions', 'extension.json' );
	}

	private function readInSkinJSONFiles() {
		$this->readInJSONFile( 'skins', 'skin.json' );
	}

	private function readInJSONFile( $baseDir, $manifestFileName ) {
		$path = $GLOBALS['IP'] . "/$baseDir";
		$dir = dir( $path );

		// phpcs:ignore MediaWiki.ControlStructures.AssignmentInControlStructures.AssignmentInControlStructures
		while ( ( $entry = $dir->read() ) !== false ) {
			if ( $entry === '.' || $entry === '..' ) {
				continue;
			}

			$manifestPath = "$path/$entry/$manifestFileName";

			if ( file_exists( $manifestPath ) ) {
				$this->jsonFiles[$entry] = FormatJson::decode(
					file_get_contents( $manifestPath ),
					true
				);
			}
		}

		$dir->close();
	}

	private function buildDocs() {
		foreach ( $this->jsonFiles as $extensionOrSkin => $manifestData ) {
			$name = $manifestData['name'];

			if ( strpos( $name, 'BlueSpice' ) !== 0 ) {
				continue;
			}

			$type = 'undefined';
			if ( isset( $manifestData['type'] ) ) {
				$type = $manifestData['type'];
			}

			$prefix = $this->getPagePrefix( $type );

			$this->output( "Creating page for '$prefix'/'$name'" );

			$this->collectData( $name, $manifestData );
			if ( $this->admin !== false ) {
				$this->collectDataForAdmin( $name, $manifestData );
			}

			$this->createWikiTextXML( $prefix, $name, true );
		}
	}

	private function getPagePrefix( $type ) {
		$prefix = 'Extensions';
		if ( $type === 'skin' ) {
			$prefix = 'Skins';
		}
		return $prefix;
	}

	private function collectData( $name, $manifestData ) {
		$this->data[$name] = [
			'Description' => $this->addMainDesc( $manifestData ),
			'Specialpages' => $this->addSpecialPages( $manifestData ),
			'Permissions' => $this->addPermissions( $manifestData ),
			'Default_User_Options' => $this->addDefaultUserOptions( $manifestData ),
			'Configuration' => $this->addConfigs( $manifestData )
		];
	}

	private function collectDataForAdmin( $name, $manifestData ) {
		$this->data[$name] += [
			'API' => $this->addApiModules( $manifestData ),
			'Hooks' => $this->addHooks( $manifestData ),
			'Extension_Functions' => $this->addExtensionFunctions( $manifestData ),
			'Integrates_into' => $this->addIntegrations( $manifestData )
		];
	}

	private function buildTemplateCall( $templateName, $templateData ) {
		$templateString = '';
		/* Template to open the table */
		if ( $this->labeledSection !== false ) {
			$templateString .= "<section begin=$templateName />";
		}
		$templateString .= "{{" . $templateName . "Open}}\n";

		/* Template for table rows */
		foreach ( $templateData as $item ) {
			$templateString .= "{{" . $templateName . "Body\n";
			foreach ( $item as $key => $value ) {
				$templateString .= "|$key = $value\n";
			}
			$templateString .= " }}\n";
		}

		/* Template to close the table*/
		$templateString .= "{{" . $templateName . "Close}}";

		if ( $this->labeledSection !== false ) {
			$templateString .= "<section end=$templateName />";
		}

		$templateString .= "\n";

		return $templateString;
	}

	private function createWikiTextXML( $prefix, $name, $useSubpages = false ) {
		$timestamp = new MWTimestamp();

		$page = [
			'title' => false,
			'content' => '',
			'timestamp' => $timestamp->getTimestamp( TS_DB )
		];

		$content = [];
		foreach ( $this->data[$name] as $key => $value ) {
			if ( empty( $value ) ) {
				continue;
			}
			$content[$key] = $value;
		}

		$page['title'] = '' . $prefix . '/' . $name;
		$page['content'] = implode( "\n", $content );

		$this->pages[] = $this->xmlTemplatePage( $page );
	}

	private function addMainDesc( $manifestData ) {
		$desc = $manifestData['desc'] ?? '';
		$descMessage = new RawMessage( $desc );
		if ( isset( $manifestData['descriptionmsg'] ) ) {
			$descMessage = Message::newFromKey( $manifestData['descriptionmsg'] );
		}

		$templateData = [];
		$templateData[] = [
				'desc' => $descMessage->inLanguage( $this->lang )->plain()
			];

		$templateString = '';
		if ( !empty( $templateData ) ) {
			$templateString = $this->buildTemplateCall( 'ExtensionMainDesc', $templateData );
		}

		return $templateString;
	}

	private function addDefaultUserOptions( $manifestData ) {
		$configs = $manifestData['DefaultUserOptions'] ?? [];

		foreach ( $configs as $name => $defaultValue ) {
			$templateData[] = [
				'name' => $name,
				"defaultValue" => $defaultValue
			];
		}
		$templateString = '';
		if ( !empty( $templateData ) ) {
			$templateString = $this->buildTemplateCall( 'ExtensionDefaultUserOptions', $templateData );
		}

		return $templateString;
	}

	private function addConfigs( $manifestData ) {
		$configPrefix = $manifestData['config_prefix'] ?? '';
		$configs = $manifestData['config'] ?? [];

		foreach ( $configs as $configName => $desc ) {

			$defaultValue = FormatJson::encode( $desc['value'] ?? $desc );

			$templateData[] = [
				'name' => "\$$configPrefix$configName",
				"defaultValue" => '<nowiki>' . $defaultValue . '</nowiki>',
				'overrideBy' => "\$bsgOverride$configName"
			];
		}
		$templateString = '';
		if ( !empty( $templateData ) ) {
			$templateString = $this->buildTemplateCall( 'ExtensionConfig', $templateData );
		}

		return $templateString;
	}

	private function addIntegrations( $manifestData ) {
		$attributes = $manifestData['attributes'] ?? [];
		foreach ( $attributes as $extName => $registries ) {
			$templateData[] = [
				'into' => $extName
			];
		}

		$templateString = '';
		if ( !empty( $templateData ) ) {
			$templateString = $this->buildTemplateCall( 'ExtensionIntegratesInto', $templateData );
		}

		return $templateString;
	}

	private function addApiModules( $manifestData ) {
		$attributes = $manifestData['APIModules'] ?? [];
		foreach ( $attributes as $key => $class ) {
			$templateData[] = [
				'key' => $key,
				'class' => $class
			];
		}

		$templateString = '';
		if ( !empty( $templateData ) ) {
			$templateString = $this->buildTemplateCall( 'ExtensionApiModules', $templateData );
		}

		return $templateString;
	}

	private function addPermissions( $manifestData ) {
		$attributes = $manifestData['attributes']['BlueSpiceFoundation']['PermissionRegistry'] ?? [];
		foreach ( $attributes as $key => $value ) {

			if ( !isset( $this->permissions[$key] ) ) {
				$this->permissions += [ $key => [] ];
			}
			$this->permissions[$key] += $value['roles'];

			foreach ( $value['roles'] as $role ) {
				if ( !isset( $this->roles[$role] ) ) {
					$this->roles += [ $role => [] ];
				}
				if ( !in_array( $key, $this->roles[$role] ) ) {
					$this->roles[$role][] = $key;
				}
			}

			$descMessage = Message::newFromKey( "right-$key" );

			$templateData[] = [
				'permission' => $key,
				'type' => $value['type'],
				'roles' => implode( ',<br>', $value['roles'] ),
				'desc' => $descMessage->inLanguage( $this->lang )->plain()
			];
		}

		$templateString = '';
		if ( !empty( $templateData ) ) {
			$templateString = $this->buildTemplateCall( 'ExtensionPermissions', $templateData );
		}

		return $templateString;
	}

	private function addHooks( $manifestData ) {
		$attributes = $manifestData['Hooks'] ?? [];
		foreach ( $attributes as $hook => $class ) {
			$callback = $class;
			if ( is_array( $class ) ) {
				$callback = implode( ',<br>', $class );
			}

			$templateData[] = [
				'hook' => $hook,
				'callback' => $callback
			];
		}

		$templateString = '';
		if ( !empty( $templateData ) ) {
			$templateString = $this->buildTemplateCall( 'ExtensionHooks', $templateData );
		}

		return $templateString;
	}

	private function addSpecialPages( $manifestData ) {
		$attributes = $manifestData['SpecialPages'] ?? [];
		foreach ( $attributes as $name => $class ) {
			$templateData[] = [
				'name' => $name,
				'class' => $class
			];
		}

		$templateString = '';
		if ( !empty( $templateData ) ) {
			$templateString = $this->buildTemplateCall( 'ExtensionSpecialPages', $templateData );
		}

		return $templateString;
	}

	private function addExtensionFunctions( $manifestData ) {
		$attributes = $manifestData['ExtensionFunctions'] ?? [];
		foreach ( $attributes as $item ) {
			$templateData[] = [
				'class' => $item
			];
		}

		$templateString = '';
		if ( !empty( $templateData ) ) {
			$templateString = $this->buildTemplateCall( 'ExtensionExtensionFunctions', $templateData );
		}

		return $templateString;
	}

	private function buildPermissionsTable() {
		$templateData = [];
		foreach ( $this->permissions as $permission => $roles ) {
			$descMessage = Message::newFromKey( "right-$permission" );

			$templateData[] = [
					'permission' => $permission,
					'roles' => implode( ',<br>', $roles ),
					'desc' => $descMessage->inLanguage( $this->lang )->plain()
				];
		}
		$templateString = $this->buildTemplateCall( 'ExtensionPermissionsTable', $templateData );

		$this->createPermissionsOrRolesPage( 'Permissions', $templateString );

		$this->output( "Creating page for Permissions" );
	}

	private function buildRolesTable() {
		$templateData = [];
		foreach ( $this->roles as $role => $permissions ) {
			$templateData[] = [
					'role' => $role,
					'permissions' => implode( ',<br>', $permissions )
				];
		}
		$templateString = $this->buildTemplateCall( 'ExtensionRolesTable', $templateData );

		$this->createPermissionsOrRolesPage( 'Roles', $templateString );

		$this->output( "Creating page for Roles" );
	}

	private function xmlTemplateOpen() {
		$xml = '<mediawiki>' . "\n";

		return $xml;
	}

	private function xmlTemplatePage( $page ) {
		$xml = '	<page>' . "\n";
		$xml .= '		<title>' . htmlspecialchars( Validator::cleanUp( $page['title'] ) ) . '</title>' . "\n";
		$xml .= '		<revision>' . "\n";
		$xml .= '			<model>wikitext</model>' . "\n";
		$xml .= '			<format>text/x-wiki</format>' . "\n";
		$xml .= '			<text xml:space="preserve" bytes="' . mb_strlen( $page['content'] ) . '">';
		$xml .= htmlspecialchars( Validator::cleanUp( $page['content'] ) );
		$xml .= '			</text>' . "\n";
		$xml .= '		</revision>' . "\n";
		$xml .= '	</page>' . "\n";

		return $xml;
	}

	private function xmlTemplateClose() {
		$xml = '</mediawiki>';

		return $xml;
	}

	private function createExportXML() {
		$xml = $this->xmlTemplateOpen();
		$xml .= implode( "\n", $this->pages );
		$xml .= $this->xmlTemplateClose();

		return $xml;
	}

	private function createOutputFile( $xml ) {
		if ( !empty( $xml ) ) {
			file_put_contents( $this->file, $xml );
		}
	}

	private function createPermissionsOrRolesPage( $name, $templateString ) {
		$timestamp = new MWTimestamp();

		$page = [
			'title' => $name,
			'content' => $templateString,
			'timestamp' => $timestamp->getTimestamp( TS_DB )
		];

		$this->pages[] = $this->xmlTemplatePage( $page );
	}

}

$maintClass = 'AutoGenWikiDocs';
require_once RUN_MAINTENANCE_IF_MAIN;
