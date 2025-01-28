<?php

namespace BlueSpice\HookHandler;

use BlueSpice\UtilityFactory;
use MediaWiki\Config\Config;
use MediaWiki\Context\RequestContext;
use MediaWiki\Hook\ApiBeforeMainHook;
use MediaWiki\Hook\BeforeParserFetchFileAndTitleHook;
use MediaWiki\Hook\BeforeParserFetchTemplateRevisionRecordHook;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Revision\RevisionLookup;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Title\TitleFactory;
use MediaWiki\User\User;

class PermissionLockdown implements
	ApiBeforeMainHook,
	BeforeParserFetchTemplateRevisionRecordHook,
	BeforeParserFetchFileAndTitleHook
{

	/** @var Config */
	private $config = null;

	/** @var TitleFactory */
	private $titleFactory = null;

	/** @var RevisionLookup */
	private $revisionLookup = null;

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
	 * @param TitleFactory $titleFactory
	 * @param RevisionLookup $revisionLookup
	 * @param UtilityFactory $utilityFactory
	 * @param bool|null $noSession
	 */
	public function __construct(
		Config $config,
		PermissionManager $permissionManager,
		TitleFactory $titleFactory,
		RevisionLookup $revisionLookup,
		UtilityFactory $utilityFactory,
		$noSession = null ) {
		$this->config = $config;
		$this->permissionManager = $permissionManager;
		$this->titleFactory = $titleFactory;
		$this->revisionLookup = $revisionLookup;
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
		$user = $this->getRelevantUser();
		if ( !$user ) {
			return;
		}
		if ( !$this->permissionManager->userCan( 'read', $user, $title ) ) {
			$skip = true;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function onBeforeParserFetchFileAndTitle( $parser, $nt, &$options, &$descQuery ) {
		$user = $this->getRelevantUser();
		if ( !$user ) {
			return;
		}
		if ( !$this->permissionManager->userCan( 'read', $user, $nt ) ) {
			$options['broken'] = true;
		}
	}

	/**
	 * @return User|null
	 * @throws \MWException
	 */
	private function getRelevantUser(): ?User {
		if ( $this->noSessionContext ) {
			// Bail out on no session entry points, since we cannot init user
			return null;
		}

		$user = $this->requestContext->getUser();
		if ( $this->config->get( 'CommandLineMode' ) ) {
			if ( !$user->isRegistered() ) {
				return $this->utilityFactory->getMaintenanceUser()->getUser();
			}
		}
		return $user;
	}

	/**
	 * Makes sure calls to the API that have a non readable page title as context are blocked
	 * @inheritDoc
	 */
	public function onApiBeforeMain( &$main ) {
		$user = $main->getUser();
		$request = $main->getRequest();

		$titleParams = [ 'title', 'titles', 'page', 'pages' ];
		foreach ( $titleParams as $param ) {
			$titleParamValue = $request->getVal( $param, null );
			if ( $titleParamValue === null ) {
				continue;
			}
			$titleParamValues = explode( '|', $titleParamValue );
			$paramMap = [];
			foreach ( $titleParamValues as $titleParamValue ) {
				$titleParamTitle = $this->titleFactory->newFromText( $titleParamValue );
				if ( !$titleParamTitle ) {
					continue;
				}
				$paramMap[$titleParamValue] = $titleParamTitle;
			}
			if ( empty( $paramMap ) ) {
				continue;
			}
			$filteredParamValue = $this->filterParamValue( $paramMap, $user );
			$request->setVal( $param, $filteredParamValue );
		}

		$pageIDParams = [ 'pageid', 'pageids' ];
		foreach ( $pageIDParams as $param ) {
			$pageIDParamValue = $request->getVal( $param, null );
			if ( $pageIDParamValue === null ) {
				continue;
			}
			$pageIDParamValues = explode( '|', $pageIDParamValue );
			$paramMap = [];
			foreach ( $pageIDParamValues as $pageIDParamValue ) {
				$pageIDParamTitle = $this->titleFactory->newFromID( $pageIDParamValue );
				if ( !$pageIDParamTitle ) {
					continue;
				}
				$paramMap[$pageIDParamValue] = $pageIDParamTitle;
			}
			if ( empty( $paramMap ) ) {
				continue;
			}
			$filteredParamValue = $this->filterParamValue( $paramMap, $user );
			$request->setVal( $param, $filteredParamValue );
		}

		$revisionIDParams = [ 'revid', 'revids', 'newid', 'oldid', 'oldids' ];
		foreach ( $revisionIDParams as $param ) {
			$revisionIDParamValue = $request->getVal( $param, null );
			if ( $revisionIDParamValue === null ) {
				continue;
			}
			$revisionIDParamValues = explode( '|', $revisionIDParamValue );
			$paramMap = [];
			foreach ( $revisionIDParamValues as $revisionIDParamValue ) {
				$revisionIDParamRevision = $this->revisionLookup->getRevisionById( $revisionIDParamValue );
				$revisionIDParamTitle = null;
				if ( $revisionIDParamRevision !== null ) {
					$pageIdentity = $revisionIDParamRevision->getPage();
					$revisionIDParamTitle = $this->titleFactory->newFromPageIdentity( $pageIdentity );
				}
				$paramMap[$revisionIDParamValue] = $revisionIDParamTitle;
			}
			if ( !empty( $paramMap ) ) {
				$filteredParamValue = $this->filterParamValue( $paramMap, $user );
				$request->setVal( $param, $filteredParamValue );
			}
		}
	}

	/**
	 * @param array $paramMap
	 * @param User $user
	 * @return string
	 */
	private function filterParamValue( array $paramMap, User $user ) {
		$filteredParamValue = [];
		foreach ( $paramMap as $paramValue => $paramTitle ) {
			$userCanRead = $this->permissionManager->userCan( 'read', $user, $paramTitle );
			if ( $paramTitle === null || $userCanRead ) {
				$filteredParamValue[] = $paramValue;
			}
		}
		return implode( '|', $filteredParamValue );
	}
}
