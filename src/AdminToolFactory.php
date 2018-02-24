<?php

namespace BlueSpice;

class AdminToolFactory {
	protected $classes = [];

	public function __construct( $classes ) {
		$this->classes = $classes;
	}

	/**
	 * @return IAdminTool[]
	 */
	public function getAll() {
		$adminTools = [];
		foreach( $this->classes as $toolId => $callback ) {
			$tool = new $callback();
			if( $tool instanceof IAdminTool === false ) {
				throw new MWException( "Class for tool '$toolId' does not implement IAdminTool" );
			}
			$adminTools[$toolId] = $tool;
		}

		return $adminTools;
	}
}
