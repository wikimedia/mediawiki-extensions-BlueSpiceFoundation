<?php

namespace BlueSpice\Html\FormField;

use Message;
use BsGroupHelper;

class GroupMultiSelect extends \HTMLMultiSelectEx {

	const PARAM_BLACKLIST = 'blacklist';

	protected $groups;

	public function __construct( $params ) {
		if ( !isset( $params[static::PARAM_BLACKLIST] ) ) {
			$params[static::PARAM_BLACKLIST] = [];
		}

		$this->loadGroups( $params );
		$this->makeOptions( $params );

		$params['options'] = $this->options;

		parent::__construct( $params );
	}

	protected function loadGroups( $params ) {
		$this->groups = BsGroupHelper::getAvailableGroups( $params );
	}

	protected function makeOptions( $params ) {
		$this->options = [];
		foreach ( $this->groups as $group ) {
			$groupDisplay = $group;
			$msg = Message::newFromKey( "group-$group" );
			if( $msg->exists() ) {
				$groupDisplay = $msg->plain()." ($group)";
			}
			$this->options[$group] = $msg->exists()
				? "{$msg->plain()} ($group)"
				: $group;
		}
	}
}
