<?php

namespace BlueSpice\Special;

use BlueSpice\LoadPlaceholderRegistry;
use BlueSpice\SpecialPage;
use Html;

abstract class ExtJSBase extends SpecialPage {

	/**
	 *
	 * @param string $par
	 */
	public function execute( $par ) {
		parent::execute( $par );

		$this->addContainer();
		$this->addJSVars();
		$this->addModules();
	}

	protected function addContainer() {
		$attributes = array_merge( [
			'id' => $this->getId(),
			'class' => implode( ' ', $this->getClasses() )
		], $this->getAttributes() );
		$container = Html::openElement( 'div', $attributes );
		$container .= $this->getLoadPlaceholder();
		$container .= Html::closeElement( 'div' );

		$this->getOutput()->addHTML( $container );
	}

	protected function addJSVars() {
		$vars = $this->getJSVars();
		if ( empty( $vars ) ) {
			return;
		}
		$this->getOutput()->addJsConfigVars( $vars );
	}

	protected function addModules() {
		$this->getOutput()->addModules( $this->getModules() );
	}

	/**
	 * @return string ID of the HTML element being added
	 */
	abstract protected function getId();

	/**
	 * @return array
	 */
	abstract protected function getModules();

	/**
	 * Return array of CSS classes to be applied to the container
	 * @return array
	 */
	protected function getClasses() {
		return [
			"extjs-load-placeholder-cnt"
		];
	}

	/**
	 * Return array of attributes to be added
	 * to the HTML element
	 *
	 * @return array [ $attr => $value ]
	 */
	protected function getAttributes() {
		return [];
	}

	/**
	 *
	 * @return string
	 */
	protected function getLoadPlaceholderTemplateName() {
		return 'ExtJSGeneric';
	}

	/**
	 * @return array [ $name => $value ]
	 */
	protected function getJSVars() {
		return [];
	}

	private function getLoadPlaceholder() {
		$registry = new LoadPlaceholderRegistry();
		return $registry->getParsedTemplate( $this->getLoadPlaceholderTemplateName() );
	}
}
