<?php

namespace BlueSpice\Hook\BeforePageDisplay;

class AddResources extends \BlueSpice\Hook\BeforePageDisplay {

	/**
	 * @return bool
	 */
	protected function doProcess() {
		$this->addModuleStyles();
		$this->addModules();
		$this->addJSConfigVars();
		return true;
	}

	protected function addModuleStyles() {
		$this->out->addModuleStyles( 'ext.bluespice.styles' );
		$this->out->addModuleStyles( 'ext.bluespice.compat.vector.styles' );
	}

	protected function addModules() {
		$this->out->addModules( 'ext.bluespice' );

		if ( $this->getConfig()->get( 'TestSystem' ) ) {
			$this->out->addModules( 'ext.bluespice.testsystem' );
		}
	}

	protected function addJSConfigVars() {
		$configs = [
			'MaxUploadSize' => [
				'php' => 1024 * 1024 * (int)ini_get( 'upload_max_filesize' ),
				'mediawiki' => $this->getConfig()->get( 'MaxUploadSize' ),
			],
			'EnableUploads' => $this->getConfig()->get( 'EnableUploads' ),
			'FileExtensions' => $this->lcNormalizeArray(
				$this->getConfig()->get( 'FileExtensions' )
			),
			'ImageExtensions' => $this->lcNormalizeArray(
				$this->getConfig()->get( 'ImageExtensions' )
			),
			'PageCollectionPrefix' => wfMessage( 'bs-pagecollection-prefix' )->inContentLanguage()->text()
		];

		if ( $this->getConfig()->get( 'TestSystem' ) ) {
			$configs['TestSystem'] = true;
		}

		foreach ( $configs as $name => $config ) {
			$this->out->addJsConfigVars( "bsg$name", $config );
		}
	}

	/**
	 * Performs lower case transformation on every item of an array and removes
	 * duplicates
	 * @param array $data One dimensional array of strings
	 * @return array
	 */
	protected function lcNormalizeArray( $data ) {
		$normalized = [];
		foreach ( $data as $item ) {
			$normalized[] = strtolower( $item );
		}
		return array_values(
			array_unique( $normalized )
		);
	}
}
