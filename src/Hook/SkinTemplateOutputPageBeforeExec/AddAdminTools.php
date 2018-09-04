<?php

namespace BlueSpice\Hook\SkinTemplateOutputPageBeforeExec;

use BlueSpice\Hook\SkinTemplateOutputPageBeforeExec;
use BlueSpice\SkinData;

class AddAdminTools extends SkinTemplateOutputPageBeforeExec {
	protected function doProcess() {
		$registry = $this->getServices()->getBSAdminToolFactory();
		$adminTools = $registry->getAll();
		$user = $this->getContext()->getUser();
		foreach ( $adminTools as $toolId => $tool ) {
			foreach ( $tool->getPermissions() as $permission ) {
				if ( $user->isAllowed( $permission ) ) {
					$this->template->data[SkinData::ADMIN_LINKS][$toolId] =
						$this->makeLinkDesc( $tool );
					break;
				}
			}
		}
	}

	/**
	 *
	 * @param \BlueSpice\IAdminTool $tool
	 * @return array
	 */
	protected function makeLinkDesc( $tool ) {
		$link = [
			'title' => $tool->getDescription(),
			'text' => $tool->getName(),
			'href' => $tool->getURL(),
			'iconClass' => implode( ' ', $tool->getClasses() ),
			'data' => $tool->getDataAttributes(),

		];
		return $link;
	}

}
