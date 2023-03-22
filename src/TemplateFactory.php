<?php

namespace BlueSpice;

use BlueSpice\Utility\TemplateHelper;
use Exception;
use MediaWiki\MediaWikiServices;

class TemplateFactory {

	/**
	 *
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var TemplateHelper
	 */
	protected $templateHelper = null;

	/**
	 *
	 * @var Template[]
	 */
	protected $intances = [];

	/**
	 *
	 * @param ExtensionAttributeBasedRegistry $registry
	 * @param TemplateHelper $templateHelper
	 */
	public function __construct( ExtensionAttributeBasedRegistry $registry,
		TemplateHelper $templateHelper ) {
		$this->registry = $registry;
		$this->templateHelper = $templateHelper;
	}

	/**
	 *
	 * @param string $name
	 * @return Template
	 */
	public function get( $name ) {
		if ( isset( $this->intances[$name] ) ) {
			return $this->intances[$name];
		}
		$type = "mustache";
		$parts = explode( TemplateHelper::SEPARATOR, $name );
		$fileExt = array_slice( $parts, -1 )[0];

		foreach ( $this->registry->getAllKeys() as $registeredType ) {
			if ( $fileExt !== $registeredType ) {
				continue;
			}
			$type = array_pop( $parts );
		}
		$name = implode( TemplateHelper::SEPARATOR, $parts );
		$callback = $this->registry->getValue( $type, null );
		if ( !is_callable( $callback ) ) {
			throw new Exception( "Unknown template type for $name" );
		}

		$this->intances[$name] = call_user_func_array( $callback, [
			MediaWikiServices::getInstance(),
			$name
		] );

		return $this->intances[$name];
	}
}
