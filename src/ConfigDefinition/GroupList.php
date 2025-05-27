<?php

namespace BlueSpice\ConfigDefinition;

use BlueSpice\Html\FormField\GroupMultiSelect;
use MediaWiki\HTMLForm\HTMLForm;
use MediaWiki\Message\Message;

abstract class GroupList extends \BlueSpice\ConfigDefinition {

	/**
	 *
	 * @return GroupMultiSelect
	 */
	public function getHtmlFormField() {
		return new GroupMultiSelect( [
			'parent' => new HTMLForm( [] ),
			'fieldname' => $this->getName(),
			'id' => $this->makeID(),
			'name' => $this->name,
			'label' => Message::newFromKey( $this->getLabelMessageKey() ),
			GroupMultiSelect::PARAM_BLACKLIST => $this->getBlacklist()
		] );
	}

	/**
	 *
	 * @return array
	 */
	protected function getBlacklist() {
		return [];
	}

}
