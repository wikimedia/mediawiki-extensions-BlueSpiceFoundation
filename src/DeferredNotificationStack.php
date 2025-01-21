<?php
namespace BlueSpice;

use MediaWiki\Context\RequestContext;
use MWException;

class DeferredNotificationStack {

	/**
	 * @var \Webrequest
	 */
	private $request = null;

	/**
	 * DeferredNotificationStack constructor.
	 * @param \Webrequest $request
	 */
	public function __construct( $request ) {
		$this->request = $request;

		if ( defined( 'MW_NO_SESSION' ) ) {
			throw new MWException( "No session available" );
		}

		if ( !empty( $this->request->getCookie( 'notificationFlag' ) ) ) {
			$this->request->getSession()->set( 'notificationInfo', [] );
		}

		$context = RequestContext::getMain();
		$user = $context->getUser();
		if ( !$user->isRegistered() ) {
			$this->request->response()->clearCookie( 'notificationFlag' );
		}
	}

	/**
	 * @param IDeferredNotification $notification
	 */
	public function push( IDeferredNotification $notification ) {
		$notifications = $this->request->getSession()->get( 'notificationInfo', [] );
		$notifications[] = $notification;

		$this->request->getSession()->set( 'notificationInfo', $notifications );
	}

	/**
	 *
	 * @return IDeferredNotification[]
	 */
	public function getDeferredNotifications() {
		return $this->request->getSession()->get( 'notificationInfo', [] );
	}
}
