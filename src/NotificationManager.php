<?php

namespace BlueSpice;

/**
 * This class serves as a mediator between extensions
 * and notifiers.
 * This should be accessed only via "BSNotificationManager" service
 */
class NotificationManager {
	/**
	 * Holds instances of all registered notifiers
	 *
	 * @var array
	 */
	protected $notifiers;

	/**
	 *
	 * @var BlueSpice\IRegistry
	 */
	protected $notificationRegistry;

	/**
	 *
	 * @var BlueSpice\ExtensionAttributeBasedRegistry
	 */
	protected $notifierRegistry;

	/**
	 *
	 * @var BlueSpice\ExtensionAttributeBasedRegistry
	 */
	protected $registrationFuncRegistry;

	/**
	 *
	 * @var \Config
	 */
	protected $config;

	public function __construct( $notifierRegistry, $regFuncRegistry, $config ) {
		$this->notificationRegistry = new NotificationRegistry();
		$this->notifierRegistry = $notifierRegistry;
		$this->registrationFuncRegistry = $regFuncRegistry;
		$this->config = $config;
	}

	/**
	 * Instantiates all registered notifiers
	 */
	public function init() {
		foreach( $this->notifierRegistry->getAllKeys() as $notifier ) {
			$notifierClass = $this->notifierRegistry->getValue( $notifier );
			$notifierInstance = new $notifierClass();
			$notifierInstance->init();

			$this->notifiers[$notifier] = $notifierInstance;
		}

		$this->runRegisterFunctions();
	}

	/**
	 * Runs functions registering all notifications
	 */
	protected function runRegisterFunctions() {
		foreach( $this->registrationFuncRegistry->getAllKeys() as $regFuncIdx ) {
			$regFunc = $this->registrationFuncRegistry->getValue( $regFuncIdx );
			//Call register function passing this manager
			call_user_func( $regFunc, $this );
		}
	}

	/**
	 * Registeres single notification
	 * Notifications must specify notifier they are supposed to use
	 *
	 * @param string $key
	 * @param \BlueSpice\INotifier $notifier
	 * @param array $params
	 * @return false if notifier is not registered
	 */
	public function registerNotification( $key, INotifier $notifier, $params ) {
		if( $this->isNotifierRegistered( $notifier ) ) {
			$this->notificationRegistry->addValue( $key, $notifier );
			return $notifier->registerNotification( $key, $params );
		}

		return false;
	}

	/**
	 * Un-registeres single notification
	 *
	 * @param string $key
	 * @param \BlueSpice\INotifier $notifier
	 * @param type $params
	 * @return false if notifier is not registered
	 */
	public function unRegisterNotification( $key, INotifier $notifier ) {
		if( $this->isNotifierRegistered( $notifier ) ) {
			return $notifier->unRegisterNotification( $key );
		}

		return false;
	}

	/**
	 * Checks if notifier is registered
	 * Param passed can be notifier key or \INotifier object
	 *
	 * @param string|\INotifier $notifier
	 * @return boolean
	 */
	public function isNotifierRegistered( $notifier ) {
		if( is_string( $notifier ) ) {
			if( isset( $this->notifiers[$notifier] ) ) {
				return true;
			}
		}

		if( $notifier instanceof INotifier ) {
			foreach( $this->notifiers as $key => $registeredNotifier ) {
				if( $notifier === $registeredNotifier ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Gets notification object for specified notification
	 * If $notifier is not specified, it will default to notifier
	 * object registered for given notification key
	 *
	 * @param string $key
	 * @param array $params
	 * @param \INotificator|null $notifier
	 * @return \INotification
	 */
	public function getNotificationObject( $key, $params, $notifier = null ) {
		if( $notifier == null ) {
			$notifier = $this->notificationRegistry->getValue( $key );
		}

		return $notifier->getNotificationObject( $key, $params );
	}

	/**
	 * Runs notification object.
	 * If $notifier is not specified, it will default to notifier
	 * object registered for given notification key
	 *
	 * @param \INotification $notification
	 * @param \INotifier|null $notifier
	 * @return \Status
	 */
	public function notify( $notification, $notifier = null ) {
		if( $notifier == null ) {
			if( $this->notificationRegistry->hasKey( $notification->getKey() ) == false ) {
				return \Status::newFatal( 'Notification not registered' );
			}
			$notifier = $this->notificationRegistry->getValue( $notification->getKey() );
		}

		return $notifier->notify( $notification );
	}

	/**
	 * Gets \INotifier object for specified key (if registered)
	 *
	 * @param string $key
	 * @return \INotifier|null
	 */
	public function getNotifier( $key ) {
		if( isset( $this->notifiers[$key] ) ) {
			return $this->notifiers[$key];
		}

		return null;
	}
}
