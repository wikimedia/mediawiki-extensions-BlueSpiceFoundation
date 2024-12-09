<?php

namespace BlueSpice\HookHandler;

use BlueSpice\UtilityFactory;
use MediaWiki\Config\Config;
use MediaWiki\Context\RequestContext;
use MediaWiki\Hook\BeforeParserFetchTemplateRevisionRecordHook;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Revision\RevisionRecord;

class PermissionLockdown implements BeforeParserFetchTemplateRevisionRecordHook {

	/** @var Config */
	private $config = null;

	/** @var PermissionManager */
	private $permissionManager = null;

	/** @var UtilityFactory */
	private $utilityFactory = null;

	/** @var RequestContext */
	private $requestContext = null;

	/** @var bool */
	private $noSessionContext = false;

	/**
	 * @param Config $config
	 * @param PermissionManager $permissionManager
	 * @param UtilityFactory $utilityFactory
	 * @param bool|null $noSession
	 */
	public function __construct(
		Config $config,
		PermissionManager $permissionManager,
		UtilityFactory $utilityFactory,
		$noSession = null ) {
		$this->config = $config;
		$this->permissionManager = $permissionManager;
		$this->utilityFactory = $utilityFactory;
		$this->requestContext = RequestContext::getMain();
		if ( $noSession === null ) {
			$this->noSessionContext = defined( 'MW_NO_SESSION' );
		}
	}

	/**
	 * Check if user can read the page that is being transcluded
	 *
	 * @inheritDoc
	 */
	public function onBeforeParserFetchTemplateRevisionRecord(
		?LinkTarget $contextTitle, LinkTarget $title,
		bool &$skip, ?RevisionRecord &$revRecord
	) {
		if ( $this->noSessionContext ) {
			// Bail out on no session entry points, since we cannot init user
			return true;
		}

		$user = $this->requestContext->getUser();
		if ( $this->config->get( 'CommandLineMode' ) ) {
			if ( !$user->isRegistered() ) {
				$user = $this->utilityFactory->getMaintenanceUser()->getUser();
			}
		}

		if ( !$this->permissionManager->userCan( 'read', $user, $title ) ) {
			$skip = true;
		}
	}
}
