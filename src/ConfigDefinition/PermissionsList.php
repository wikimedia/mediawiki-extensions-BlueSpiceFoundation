<?php

namespace BlueSpice\ConfigDefinition;

use BlueSpice\Html\FormField\PermissionMultiSelect;

abstract class PermissionsList extends \BlueSpice\ConfigDefinition {

	public function getHtmlFormField() {
		return new PermissionMultiSelect( [
			'parent' => new \HTMLForm( [] ),
			'fieldname' => $this->getName(),
			'id' => $this->makeID(),
			'name' => $this->name,
			'label' => wfMessage( $this->getLabelMessageKey() )->plain(),
			'type' => $this->getPermissionType()
		] );
	}

	protected function getPermissionType() {
		return 'namespace';
	}

}
