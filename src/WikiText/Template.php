<?php

namespace BlueSpice\WikiText;

class Template {
	/**
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 *
	 * @var string
	 */
	protected $params = [];

	/**
	 *
	 * @var boolean
	 */
	protected $renderFormatted = true;

	protected $buffer = [];

	/**
	 *
	 * @param string $name
	 * @param array $params
	 */
	public function __construct( $name, $params ) {
		$this->name = $name;
		$this->params = $params;
	}

	/**
	 *
	 * @param int|string $paramNameorIndex
	 * @param mixed $paramValue
	 * @return Template
	 */
	public function set( $paramNameorIndex, $paramValue ) {
		$this->params[$paramNameorIndex] = $paramValue;
		return $this;
	}

	/**
	 *
	 * @param int|string $paramNameorIndex
	 * @param mixed $default
	 * @return mixed
	 */
	public function get( $paramNameorIndex, $default = '' ) {
		if( isset( $this->params[$paramNameorIndex] ) ) {
			return $this->params[$paramNameorIndex];
		}
		return $default;
	}

	/**
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 *
	 * @param string $name
	 * @return Template Description
	 */
	public function setName( $name ) {
		$this->name = $name;
		return $this;
	}

	/**
	 *
	 * @param boolean $renderFormatted
	 * @return Template
	 */
	public function setRenderFormatted( $renderFormatted = true ) {
		$this->renderFormatted = $renderFormatted;
		return $this;
	}

	/**
	 * @return string
	 */
	public function render() {
		$this->clearBuffer();
		$this->openCurlies();
		$this->appendName();
		$this->appendParams();
		$this->closeCurlies();

		return implode( '', $this->buffer );
	}

	/**
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}

	protected function clearBuffer() {
		$this->buffer = [];
	}

	protected function openCurlies() {
		$this->buffer[] = '{{';
	}

	protected function closeCurlies() {
		$this->buffer[] = '}}';
	}

	protected function appendName() {
		$this->buffer[] = $this->name;
		if( $this->renderFormatted ) {
			$this->buffer[] = "\n";
		}
	}

	protected function appendParams() {
		foreach( $this->params as $paramNameOrIndex => $paramValue ) {
			$this->buffer[] = "|";
			$isNamedParameter = false;
			if( !is_numeric( $paramNameOrIndex ) ) {
				$this->buffer[] = "$paramNameOrIndex =";
				$isNamedParameter = true;
			}

			$this->appendParamValue( $paramValue, $isNamedParameter );

			if( $this->renderFormatted ) {
				$this->buffer[] = "\n";
			}
		}
	}

	protected $specialWikiTextMarkupFirstChars = [ '*', '#', ':' ];

	protected function appendParamValue( $paramValue, $isNamedParameter ) {
		$preparedParamValue = $this->prepareParamValue( $paramValue );
		$firstChar = substr( $preparedParamValue, 0, 1 );

		if( in_array( $firstChar, $this->specialWikiTextMarkupFirstChars ) ) {
			$this->buffer[] = "\n";
		}
		else if( $isNamedParameter ) {
			$this->buffer[] = ' ';
		}

		$this->buffer[] = $preparedParamValue;
	}

	protected function prepareParamValue( $paramValue ) {
		if( is_array(  $paramValue ) ) {
			$newParamValue = implode( '', $paramValue );
		}
		else {
			$newParamValue = $paramValue;
		}

		return trim( $newParamValue );
	}

}
