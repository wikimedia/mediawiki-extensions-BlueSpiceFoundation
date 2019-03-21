<?php

namespace BlueSpice\ConfigDefinition;

use HTMLForm;
use BlueSpice\Html\FormField\GroupMultiSelect;

abstract class GroupList extends \BlueSpice\ConfigDefinition {

	public function getHtmlFormField() {
		return new GroupMultiSelect( [
			'parent' => new HTMLForm( [] ),
			'fieldname' => $this->getName(),
			'id' => $this->makeID(),
			'name' => $this->name,
			'label' => \Message::newFromKey( $this->getLabelMessageKey() ),
			GroupMultiSelect::PARAM_BLACKLIST => $this->getBlacklist()
		] );
	}

	protected function getBlacklist() {
		return [];
	}

}
