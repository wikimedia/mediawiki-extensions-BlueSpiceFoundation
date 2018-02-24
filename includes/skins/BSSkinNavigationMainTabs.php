<?php

class BSSkinNavigationMainTabs extends BSSkinTabs {
	protected function getTabs() {
		return $this->getSkinTemplate()->get( 'bs_navigation_main', array() );
	}

	protected function getHeading() {
		return $this->getSkinTemplate()->getMsg( 'navigation-heading' )->escaped();
	}

	protected function getTabIndexCookieName() {
		return 'bs-skin-tab-navigationTabs';
	}

	protected function getTabContentClasses($key, $item) {
		return array( 'bs-nav-tab' );
	}

	protected function getTabContentID($key, $item) {
		return 'bs-nav-section-'.$key;
	}

	protected function getContainerID() {
		return 'bs-nav-sections';
	}

	protected function getTabListID() {
		return 'bs-nav-tabs';
	}

	protected function getTabAnchorClasses($key, $item) {
		return array( 'bs-nav-tab-icon '.$item['class'] );
	}

	public function getTabAnchorText($key, $item) {
		return '<span>'.$item['label'].'</span>';
	}
}
