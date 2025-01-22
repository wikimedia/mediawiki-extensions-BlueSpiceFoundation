<?php

namespace BlueSpice\Html\FormField;

use BsGroupHelper;
use MediaWiki\Message\Message;

class GroupMultiSelect extends \HTMLMultiSelectEx {

	public const PARAM_BLACKLIST = 'blacklist';

	protected $groups;

	/**
	 *
	 * @param array $params
	 */
	public function __construct( $params ) {
		if ( !isset( $params[static::PARAM_BLACKLIST] ) ) {
			$params[static::PARAM_BLACKLIST] = [];
		}

		$this->loadGroups( $params );
		$this->makeOptions( $params );

		$params['options'] = $this->options;

		parent::__construct( $params );
	}

	/**
	 *
	 * @param array $params
	 */
	protected function loadGroups( $params ) {
		$this->groups = BsGroupHelper::getAvailableGroups( $params );
	}

	/**
	 *
	 * @param array $params
	 */
	protected function makeOptions( $params ) {
		$this->options = [];
		foreach ( $this->groups as $group ) {
			$msg = $this->msg( "group-$group" );
			$this->options[$group] = $msg->exists()
				? "{$msg->plain()} ($group)"
				: $group;
		}
	}

	/**
	 *
	 * @param array $value
	 * @param array $alldata
	 * @return Message|true
	 */
	public function validate( $value, $alldata ) {
		if ( $this->isHidden( $alldata ) ) {
			return true;
		}

		if ( isset( $this->mParams['required'] )
			&& $this->mParams['required'] !== false
			&& empty( $value )
		) {
			return $this->msg( 'htmlform-required' );
		}

		if ( isset( $this->mValidationCallback ) ) {
			return ( $this->mValidationCallback )( $value, $alldata, $this->mParent );
		}

		if ( !is_array( $value ) ) {
			return $this->msg( 'htmlform-select-badoption' );
		}

		# If all options are valid, array_intersect of the valid options
		# and the provided options will return the provided options.
		$validOptions = array_keys( $this->getOptions() );

		$validValues = array_intersect( $value, $validOptions );
		if ( count( $validValues ) == count( $value ) ) {
			return true;
		} else {
			return $this->msg( 'htmlform-select-badoption' );
		}
	}
}
