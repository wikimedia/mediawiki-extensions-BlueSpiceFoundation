<?php

namespace BlueSpice\Html\OOUI;

/**
 * This class is here due to compatibility.
 * It will no longer be needed once MW core
 * requires oojs/oojs-ui v0.27.0 at least
 */
class NumberInputWidget extends \OOUI\TextInputWidget {

	protected $pageStep;
	protected $isInteger;
	protected $showButtons;

	/**
	 * @param array $config Configuration options
	 *      - string $config['type'] HTML tag `type` attribute: 'text', 'password', 'email',
	 *          'url' or 'number'. (default: 'text')
	 *      - string $config['placeholder'] Placeholder text
	 *      - bool $config['autofocus'] Ask the browser to focus this widget, using the 'autofocus'
	 *          HTML attribute (default: false)
	 *      - bool $config['readOnly'] Prevent changes (default: false)
	 *      - int $config['maxLength'] Maximum allowed number of characters to input
	 *          For unfortunate historical reasons, this counts the number of UTF-16 code units rather
	 *          than Unicode codepoints, which means that codepoints outside the Basic Multilingual
	 *          Plane (e.g. many emojis) count as 2 characters each.
	 *      - bool $config['required'] Mark the field as required.
	 *          Implies `indicator: 'required'`. Note that `false` & setting `indicator: 'required'
	 *          will result in no indicator shown. (default: false)
	 *      - bool $config['autocomplete'] If the field should support autocomplete
	 *          or not (default: true)
	 *      - bool $config['spellcheck'] If the field should support spellcheck
	 *          or not (default: browser-dependent)
	 * @param-taint $config escapes_html
	 */
	public function __construct( array $config = [] ) {
		// Config initialization
		$config = array_merge( [
			'step' => 1
		], $config );

		$config['type'] = 'number';
		$config['multiline'] = false;

		// Parent constructor
		parent::__construct( $config );

		if ( isset( $config['min'] ) ) {
			$this->input->setAttributes( [ 'min' => $config['min'] ] );
		}

		if ( isset( $config['max'] ) ) {
			$this->input->setAttributes( [ 'max' => $config['max'] ] );
		}

		$this->input->setAttributes( [ 'step' => $config['step'] ] );

		if ( isset( $config['pageStep'] ) ) {
			$this->pageStep = $config['pageStep'];
		}

		if ( isset( $config['isInteger'] ) ) {
			$this->isInteger = $config['isInteger'];
		}

		if ( isset( $config['showButtons'] ) ) {
			$this->showButtons = $config['showButtons'];
		}

		$this->addClasses( [
			'oo-ui-numberInputWidget',
			'oo-ui-numberInputWidget-php',
		] );
	}

	/**
	 *
	 * @param array &$config
	 * @return array
	 */
	public function getConfig( &$config ) {
		$min = $this->input->getAttribute( 'min' );
		if ( $min !== null ) {
			$config['min'] = $min;
		}
		$max = $this->input->getAttribute( 'max' );
		if ( $max !== null ) {
			$config['max'] = $max;
		}
		$config['step'] = $this->input->getAttribute( 'step' );
		if ( $this->pageStep !== null ) {
			$config['pageStep'] = $this->pageStep;
		}
		if ( $this->isInteger !== null ) {
			$config['isInteger'] = $this->isInteger;
		}
		if ( $this->showButtons !== null ) {
			$config['showButtons'] = $this->showButtons;
		}
		return parent::getConfig( $config );
	}

	/**
	 *
	 * @return string
	 */
	protected function getJavaScriptClassName() {
		return "OO.ui.NumberInputWidget";
	}
}
