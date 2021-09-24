<?php

namespace BlueSpice;

/**
 * This class serves as a mediator between extensions
 * and notifiers.
 * This should be accessed only via "BSNotificationManager" service
 */
class NotificationManager {
	/**
	 *
	 * @var INotifier
	 */
	protected $notifier;

	/**
	 *
	 * @var IRegistry
	 */
	protected $notificationRegistry;

	/**
	 *
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $registrationFuncRegistry;

	/**
	 *
	 * @var \Config
	 */
	protected $config;

	/**
	 *
	 * @param ExtensionAttributeBasedRegistry $regFuncRegistry
	 * @param INotifier $notifier
	 */
	public function __construct( $regFuncRegistry, $notifier ) {
		$this->notificationRegistry = new NotificationRegistry();
		$this->registrationFuncRegistry = $regFuncRegistry;
		$this->notifier = $notifier;

		$this->init();
	}

	/**
	 * Instantiates all registered notifiers
	 */
	public function init() {
		$this->notifier->init();

		$this->runRegisterFunctions();
	}

	/**
	 * Runs functions registering all notifications
	 */
	protected function runRegisterFunctions() {
		foreach ( $this->registrationFuncRegistry->getAllKeys() as $regFuncIdx ) {
			$regFunc = $this->registrationFuncRegistry->getValue( $regFuncIdx );
			// Call register function passing this manager
			call_user_func( $regFunc, $this );
		}
	}

	/**
	 * Registeres notification category
	 *
	 * @param string $key
	 * @param array $params
	 * @param INotifier|null $notifier
	 */
	public function registerNotificationCategory( $key, $params = [], INotifier $notifier = null ) {
		if ( $notifier == null || $notifier instanceof INotifier == false ) {
			$notifier = $this->notifier;
		}

		$notifier->registerNotificationCategory( $key, $params );
	}

	/**
	 * Registeres single notification
	 *
	 * @param string $key
	 * @param array $params
	 * @param INotifier|null $notifier
	 */
	public function registerNotification( $key, $params, INotifier $notifier = null ) {
		if ( $notifier == null || $notifier instanceof INotifier == false ) {
			$notifier = $this->notifier;
		}

		$this->notificationRegistry->addValue( $key, $notifier );
		$notifier->registerNotification( $key, $params );
	}

	/**
	 * Un-registeres single notification
	 *
	 * @param string $key
	 * @param INotifier|null $notifier
	 */
	public function unRegisterNotification( $key, $notifier = null ) {
		if ( $notifier == null || $notifier instanceof INotifier == false ) {
			$notifier = $this->notifier;
		}

		$notifier->unRegisterNotification( $key );
	}

	/**
	 * Gets notification object for specified notification
	 * If $notifier is not specified, it will default to notifier
	 * object registered for given notification key
	 *
	 * @param string $key
	 * @param array $params
	 * @param INotification|null $notifier
	 * @return INotification
	 */
	public function getNotificationObject( $key, $params, $notifier = null ) {
		if ( $notifier == null || $notifier instanceof INotifier == false ) {
			$notifier = $this->notifier;
		}

		return $notifier->getNotificationObject( $key, $params );
	}

	/**
	 * Runs notification object.
	 * If $notifier is not specified, it will default to notifier
	 * object registered for given notification key
	 *
	 * @param INotification $notification
	 * @param INotifier|null $notifier
	 * @return \Status
	 */
	public function notify( $notification, $notifier = null ) {
		if ( $notifier == null || $notifier instanceof INotifier == false ) {
			if ( $this->notificationRegistry->hasKey( $notification->getKey() ) == false ) {
				return \Status::newFatal( 'Notification not registered' );
			}
			$notifier = $this->notificationRegistry->getValue( $notification->getKey() );
		}

		return $notifier->notify( $notification );
	}

	/**
	 * Gets INotifier instance
	 *
	 * @return INotifier|null
	 */
	public function getNotifier() {
		return $this->notifier;
	}
}
