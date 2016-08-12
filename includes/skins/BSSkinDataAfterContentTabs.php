<?php

class BSSkinDataAfterContentTabs extends BSSkinTabs {
	protected function getTabs() {
		return $this->getSkinTemplate()->get( 'bs_dataAfterContent', array() );
	}

	protected function getHeading() {
		return '';
	}

	protected function getTabIndexCookieName() {
		return 'bs-skin-tab-dataAfterContent';
	}

	protected function getContainerID() {
		return 'bs-data-after-content';
	}

	protected function getTabListID() {
		return 'bs-data-after-content-tabs';
	}

}