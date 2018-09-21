<?php

namespace BlueSpice;

use BlueSpice\IPageTool;

class PageToolFactory {

	/**
	 *
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var \IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var \Config
	 */
	protected $config = null;

	/**
	 *
	 * @param ExtensionAttributeBasedRegistry $registry
	 * @param \IContextSource $context
	 * @param \Config $config
	 */
	public function __construct( $registry, $context, $config ) {
		$this->registry = $registry;
		$this->context = $context;
		$this->config = $config;
	}

	/**
	 * @return IPageTool[]
	 */
	public function getAll() {
		$pageTools = [];
		foreach( $this->registry->getAllKeys() as $toolId ) {
			$callback = $this->registry->getValue( $toolId );

			$tool = call_user_func_array( $callback, [ $this->context, $this->config ] );
			if( $tool instanceof IPageTool === false ) {
				throw new \MWException( "Class for tool '$toolId' does not implement IPageTool" );
			}
			$pageTools[$toolId] = $tool;
		}

		usort( $pageTools, function( $a, $b ) {
			return $a->getPosition() > $b->getPosition();
		});

		return $pageTools;
	}
}
