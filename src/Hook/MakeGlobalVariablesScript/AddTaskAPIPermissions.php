<?php

namespace BlueSpice\Hook\MakeGlobalVariablesScript;

use MediaWiki\Api\ApiMain;

/**
 * provide task permission data for current user to be used in js ui elements,
 * eg show / hide elements get all registered api modules
 */
class AddTaskAPIPermissions extends \BlueSpice\Hook\MakeGlobalVariablesScript {

	protected function skipProcessing() {
		if ( !$this->getContext()->getUser()->isRegistered() ) {
			return true;
		}
		$isAllowed = $this->getServices()->getPermissionManager()->userHasRight(
			$this->getContext()->getUser(),
			'read'
		);
		if ( !$isAllowed ) {
			return true;
		}
		if ( $this->getConfig()->get( 'ReadOnly' ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Add js vars with users task permissions if data given from some
	 * apitaskbase class
	 * format: "bsgTaskAPIPermissions":{
	 * "interwikilinks":{ //trimmed class name without "bsapitasks" and "manager"
	 * "editInterWikiLink":true, //aTasks with results from checkTaskPermission()
	 * "removeInterWikiLink":true //...
	 * },
	 * ...
	 * }
	 * example to get value in js (don't forget to catch unavailable values):
	 * mw.config.get( 'bsgTaskAPIPermissions' ).interwikilinks.editInterWikiLink //true;
	 * @return bool
	 */
	protected function doProcess() {
		$taskPermissions = [];
		$context = new \BlueSpice\Context(
			$this->getContext(),
			$this->getConfig()
		);
		foreach ( $this->getConfig()->get( 'APIModules' ) as $name => $module ) {
			if ( !is_subclass_of( $module, \BSApiTasksBase::class ) ) {
				continue;
			}
			$api = new $module( new ApiMain( $context ), $name );
			$taskRequest = $api->task_getUserTaskPermissions( (object)[] );
			$key = preg_replace(
				[ '/^bs-/', '/-tasks$/', '/-/' ],
				[ '', '', '_' ],
				$name
			);
			$taskPermissions[$key] = $taskRequest->payload;
		}

		$this->vars['bsgTaskAPIPermissions'] = (object)$taskPermissions;
		return true;
	}

}
