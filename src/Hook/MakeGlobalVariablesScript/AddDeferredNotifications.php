<?php

namespace BlueSpice\Hook\MakeGlobalVariablesScript;

class AddDeferredNotifications extends \BlueSpice\Hook\MakeGlobalVariablesScript {

	protected function doProcess() {
		$deferredNotifications = $this->getDeferredNotifications();

		$stack = [];
		foreach ( $deferredNotifications as $deferredNotification ) {
			$stack[] = [
				$deferredNotification->getMessage()->plain(),
				$deferredNotification->getOptions()
			];
		}

		$this->out->addJsConfigVars( 'bsgDeferredNotifications', $stack );

		return true;
	}

	/**
	 *
	 * @return \BlueSpice\IDeferredNotification[]
	 */
	private function getDeferredNotifications() {
		$deferredNotificationsStack = $this->getServices()->getService( 'BSDeferredNotificationStack' );
		return $deferredNotificationsStack->getDeferredNotifications();
	}
}
