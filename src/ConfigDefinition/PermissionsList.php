<?php

namespace BlueSpice\ConfigDefinition;

use BlueSpice\Html\FormField\PermissionMultiSelect;
use MediaWiki\Context\RequestContext;
use MediaWiki\HTMLForm\HTMLForm;

abstract class PermissionsList extends \BlueSpice\ConfigDefinition {

	/**
	 *
	 * @return PermissionMultiSelect
	 */
	public function getHtmlFormField() {
		return new PermissionMultiSelect( [
			'parent' => new HTMLForm( [], RequestContext::getMain() ),
			'fieldname' => $this->getName(),
			'id' => $this->makeID(),
			'name' => $this->name,
			'label' => wfMessage( $this->getLabelMessageKey() )->text(),
			'type' => $this->getPermissionType()
		] );
	}

	/**
	 *
	 * @return string
	 */
	protected function getPermissionType() {
		return 'namespace';
	}

}
