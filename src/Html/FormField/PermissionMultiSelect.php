<?php

namespace BlueSpice\Html\FormField;

use MediaWiki\MediaWikiServices;

class PermissionMultiSelect extends \HTMLMultiSelectEx {

	private $params;

	/**
	 *
	 * @param array $params
	 */
	public function __construct( $params ) {
		if ( isset( $params['type'] ) ) {
			$type = $params['type'];
		} else {
			$type = 'namespace';
		}

		$this->loadPermissions();
		$this->makeOptions( $type );

		$params['options'] = $this->options;

		parent::__construct( $params );
	}

	protected function loadPermissions() {
		$services = MediaWikiServices::getInstance();
		$config = $services->getConfigFactory()->makeConfig( 'bsg' );
		$this->permissions = $config->get( 'PermissionConfigDefault' );
	}

	/**
	 *
	 * @param string $type
	 */
	protected function makeOptions( $type ) {
		$this->options = [];
		foreach ( $this->permissions as $permKey => $permVal ) {
			if ( empty( $type ) || ( $permVal['type'] === $type ) ) {
				$name = wfMessage( 'right-' . $permKey )->exists() ?
					wfMessage( 'right-' . $permKey )->text() :
					$permKey;
				$this->options[$permKey] = $name;
			}
		}
	}
}
