<?php

namespace BlueSpice\Hook;

use BlueSpice\Hook;

abstract class EditPageGetCheckboxesDefinition extends Hook {

	/**
	 *
	 * @var \EditPage
	 */
	protected $editPage = null;

	/**
	 *
	 * @var array
	 */
	protected $checkboxes = null;

	/**
	 *
	 * @param \EditPage $editPage
	 * @param array &$checkboxes
	 * @return bool
	 */
	public static function callback( $editPage, &$checkboxes ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$editPage,
			$checkboxes
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Config $config
	 * @param \EditPage $editPage
	 * @param array &$checkboxes
	 */
	public function __construct( $context, $config, $editPage, &$checkboxes ) {
		parent::__construct( $context, $config );

		$this->editPage = $editPage;
		$this->checkboxes = &$checkboxes;
	}
}
