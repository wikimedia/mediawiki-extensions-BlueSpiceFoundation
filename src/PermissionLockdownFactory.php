<?php

namespace BlueSpice;

use BlueSpice\Permission\Lockdown;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\MediaWikiServices;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Status\Status;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

class PermissionLockdownFactory {

	/**
	 *
	 * @var ExtensionAttributeBasedRegistry
	 */
	protected $registry = null;

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @var IContextSource
	 */
	protected $context = null;

	/**
	 *
	 * @var Lockdown[]
	 */
	protected $instances = [];

	/** @var null */
	private $modules = null;

	/**
	 *
	 * @param ExtensionAttributeBasedRegistry $registry
	 * @param Config $config
	 * @param IContextSource $context
	 */
	public function __construct( ExtensionAttributeBasedRegistry $registry, Config $config,
		IContextSource $context ) {
		$this->registry = $registry;
		$this->config = $config;
		$this->context = $context;
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @return Lockdown
	 */
	public function newFromTitleAndUserRelation( Title $title, User $user ) {
		$cacheKey = $this->getCacheKey( $title, $user );
		if ( !isset( $this->instances[$cacheKey] ) ) {
			$this->instances[$cacheKey] = $this->newLockdown( $title, $user );
		}
		return $this->instances[$cacheKey];
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @return Lockdown
	 */
	protected function newLockdown( Title $title, User $user ) {
		if ( $this->modules === null ) {
			$this->setModules();
		}
		return new Lockdown( $this->config, $title, $user, $this->modules );
	}

	/**
	 *
	 */
	protected function setModules() {
		$this->modules = [];
		foreach ( $this->registry->getAllKeys() as $key ) {
			$this->modules[] = call_user_func_array( $this->registry->getValue( $key ), [
				$this->config,
				$this->context,
				MediaWikiServices::getInstance(),
			] );
		}
	}

	/**
	 *
	 * @param Title $title
	 * @param User $user
	 * @return string
	 */
	protected function getCacheKey( Title $title, User $user ) {
		return "{$title->getNamespace()}-{$title->getDBkey()}-{$user->getId()}";
	}

	/**
	 * Can $user perform $action on a page?
	 *
	 * The method is intended to wrap around PermissionManager::userCan(), to be
	 * able to return a Status wich contains a message exactly why the user can
	 * not access the given action in permission error case
	 *
	 * @see PermissionManager::userCan()
	 *
	 * @param Title $title
	 * @param string $action
	 * @param User|null $user
	 * @param string $rigor One of PermissionManager::RIGOR_ constants
	 *   - RIGOR_QUICK  : does cheap permission checks from replica DBs (usable for GUI creation)
	 *   - RIGOR_FULL   : does cheap and expensive checks possibly from a replica DB
	 *   - RIGOR_SECURE : does cheap and expensive checks, using the primary as needed
	 *
	 * @return Status
	 */
	public function userCan( Title $title, $action = 'read', ?User $user = null,
		$rigor = PermissionManager::RIGOR_SECURE ) {
		$status = Status::newGood();
		if ( !$user ) {
			$user = $this->context->getUser();
		}
		$result = MediaWikiServices::getInstance()->getPermissionManager()->userCan(
			$action,
			$user,
			$title,
			$rigor
		);
		if ( !$result ) {
			$lockdown = $this->newFromTitleAndUserRelation( $title, $user );
			$status->merge( $lockdown->getLockState( $action ) );
		}

		return $status;
	}

}
