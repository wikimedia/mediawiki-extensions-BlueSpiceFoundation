<?php

namespace BlueSpice\Hook\BeforePageDisplay;

class AddResources extends \BlueSpice\Hook\BeforePageDisplay {

	protected function doProcess() {
		$this->addModuleStyles();
		$this->overwriteGlobals();
		$this->addModules();
		$this->addJSConfigVars();
		$this->addLegacyJSConfigVars();
	}

	protected function overwriteGlobals() {
		$GLOBALS['wgFavicon'] = $this->getConfig()->get( 'Favicon' );
		$GLOBALS['wgLogo'] = $this->getConfig()->get( 'Logo' );
	}

	protected function addModuleStyles() {
		$this->out->addModuleStyles( 'ext.bluespice.styles' );
		$this->out->addModuleStyles( 'ext.bluespice.compat.vector.styles' );
	}

	protected function addModules() {
		$this->out->addModules( 'ext.bluespice' );

		if( $this->getConfig()->get( 'TestSystem' ) ) {
			$this->out->addModules( 'ext.bluespice.testsystem' );
		}
	}

	protected function addJSConfigVars() {
		$configs = [
			'MaxUploadSize' => [
				'php' => 1024 * 1024* (int)ini_get( 'upload_max_filesize' ),
				'mediawiki' => $this->getConfig()->get( 'MaxUploadSize' ),
			],
			'EnableUploads' => $this->getConfig()->get( 'EnableUploads' ),
			'FileExtensions' => $this->lcNormalizeArray(
				$this->getConfig()->get( 'FileExtensions' )
			),
			'ImageExtensions' => $this->lcNormalizeArray(
				$this->getConfig()->get( 'ImageExtensions' )
			),
			'IsWindows' => wfIsWindows(),
			'ArticlePreviewCaptureNotDefault' => $this->getArticlePreviewCaptureNotDefault(),
		];

		if( $this->getConfig()->get( 'TestSystem' ) ) {
			$configs['TestSystem'] = true;
		}

		foreach( $configs as $name => $config ) {
			$this->out->addJsConfigVars( "bsg$name", $config );
		}

		$this->addLecagyJSConfigVarNames( $configs );
	}

	/**
	 * Old var names "bs<Config>" are still heavily in use
	 * @param array $configs
	 */
	protected function addLecagyJSConfigVarNames( $configs ) {
		foreach( $configs as $name => $config ) {
			$this->out->addJsConfigVars( "bs$name", $config );
		}
	}

	/**
	 * DEPRECATED!
	 * @deprecated since version 3.0.0 - \BsConfig is not used anymore
	 */
	protected function addLegacyJSConfigVars() {
		$scriptSettings = \BsConfig::getScriptSettings();
		\Hooks::run( 'BsFoundationBeforeMakeGlobalVariablesScript', [
			$this->out->getUser(),
			&$scriptSettings
		]);

		foreach ( $scriptSettings as $setting ) {
			$value = $setting->getValue();
			if( $setting->getOptions() & \BsConfig::TYPE_JSON ) {
				$value = json_decode( $value );
			}
			// All settings are outputed like this: setting bsVisualEditorUse = true
			// VisualEditor = $setting->getExtension()
			// Use = $setting->getName()
			// true = $sValue
			$this->out->addJsConfigVars(
				"bs{$setting->getExtension()}{$setting->getName()}",
				$value
			);
		}
	}

	/**
	 * Performs lower case transformation on every item of an array and removes
	 * duplicates
	 * @param array $data One dimensional array of strings
	 * @return array
	 */
	protected function lcNormalizeArray( $data ) {
		$normalized = array();
		foreach( $data as $item ) {
			$normalized[] = strtolower( $item );
		}
		return array_values(
			array_unique( $normalized )
		);
	}

	protected function getArticlePreviewCaptureNotDefault() {
		$extRegistry = \ExtensionRegistry::getInstance();
		$modules = $extRegistry->getAttribute(
			'BlueSpiceFoundationDynamicFileRegistry'
		);
		$articlePreviewCaptureNotDefault = false;
		foreach( $modules as $key => $module ) {
			if( $key !== "articlepreviewimage" ) {
				continue;
			}

			$articlePreviewCaptureNotDefault
				= $module !== "\\BlueSpice\\DynamicFileDispatcher\\ArticlePreviewImage";
		}
		return $articlePreviewCaptureNotDefault;
	}
}
