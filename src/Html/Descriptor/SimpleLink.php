<?php

namespace BlueSpice\Html\Descriptor;

use Exception;
use MediaWiki\Language\RawMessage;
use MediaWiki\Message\Message;

class SimpleLink implements ILink {

	public const CSSCLASSES = 'css-classes';
	public const DATAATTRIBUTES = 'data-attributes';
	public const HTMLID = 'html-id';
	public const ICON = 'icon';
	public const LABEL = 'label';
	public const TOOLTIP = 'tooltip';
	public const HREF = 'href';

	protected $data = [];

	/**
	 *
	 * @param array $data
	 */
	public function __construct( $data ) {
		$this->data = $data;
		foreach ( $this->getRequiredFields()  as $fieldName ) {
			if ( !isset( $this->data[ $fieldName ] ) ) {
				throw new Exception( "Field '$fieldName' must be provided!" );
			}
		}
	}

	/**
	 *
	 * @return string[]
	 */
	public function getCSSClasses() {
		return $this->getFromData( static::CSSCLASSES, [] );
	}

	/**
	 *
	 * @return array
	 */
	public function getDataAttributes() {
		return $this->getFromData( static::DATAATTRIBUTES, [] );
	}

	/**
	 *
	 * @return string
	 */
	public function getHtmlId() {
		return $this->getFromData( static::HTMLID, '' );
	}

	/**
	 *
	 * @return string
	 */
	public function getIcon() {
		return $this->getFromData( static::ICON, '' );
	}

	/**
	 *
	 * @return Message
	 */
	public function getLabel() {
		return new RawMessage( $this->getFromData( static::LABEL, '' ) );
	}

	/**
	 *
	 * @return Message
	 */
	public function getTooltip() {
		return new RawMessage( $this->getFromData( static::TOOLTIP, '' ) );
	}

	/**
	 * @return string
	 */
	public function getHref() {
		return $this->getFromData( static::HREF, '' );
	}

	/**
	 *
	 * @param string $fieldName
	 * @param mixed $default
	 * @return mixed
	 */
	protected function getFromData( $fieldName, $default ) {
		if ( !isset( $this->data[$fieldName] ) ) {
			return $default;
		}

		return $this->data[$fieldName];
	}

	/**
	 * @return string[]
	 */
	protected function getRequiredFields() {
		return [ static::LABEL, static::TOOLTIP, static::HREF ];
	}

}
