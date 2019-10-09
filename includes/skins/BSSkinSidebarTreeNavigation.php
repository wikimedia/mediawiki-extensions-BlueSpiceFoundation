<?php

class BSSkinSidebarTreeNavigation extends BSSkinTreeNavigation {

	/**
	 *
	 * @return string
	 */
	protected function getContainerID() {
		return 'bs-sidebar';
	}

	/**
	 *
	 * @return \BSTreeNode
	 */
	protected function makeTreeRootNode() {
		$sidebarTreeParser = new BSSkinSidebarTreeParser( $this->getSkinTemplate() );
		return $sidebarTreeParser->parse();
	}

}
