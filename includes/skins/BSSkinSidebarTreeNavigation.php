<?php

class BSSkinSidebarTreeNavigation extends BSSkinTreeNavigation {

	protected function getContainerID() {
		return 'bs-sidebar';
	}

	protected function makeTreeRootNode() {
		$sidebarTreeParser = new BSSkinSidebarTreeParser( $this->getSkinTemplate() );
		return $sidebarTreeParser->parse();
	}

}
