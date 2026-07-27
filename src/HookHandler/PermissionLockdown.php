<?php

namespace BlueSpice\HookHandler;

use BlueSpice\UtilityFactory;
use MediaWiki\Api\ApiQuery;
use MediaWiki\Api\ApiResult;
use MediaWiki\Api\Hook\APIAfterExecuteHook;
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
	APIAfterExecuteHook,
	BeforeParserFetchTemplateRevisionRecordHook,
	BeforeParserFetchFileAndTitleHook
{

	/**
	 * Map of API query list module names to the result field(s) that hold
	 * a page title. Each entry is:
	 *   'titleField' => field name for the prefixed title string (default: 'title')
	 *   'nsField'    => field name for the namespace id (default: 'ns')
	 *
	 * Modules not listed here are not filtered.
	 */
	private const LIST_MODULE_CONFIG = [
		'allpages' => [],
		'alllinks' => [],
		'allfileusages' => [],
		'allredirects' => [],
		'alltransclusions' => [],
		'allimages' => [ 'nsField' => null ],
		'backlinks' => [],
		'embeddedin' => [],
		'imageusage' => [],
		'categorymembers' => [],
		'exturlusage' => [],
		'iwbacklinks' => [],
		'langbacklinks' => [],
		'logevents' => [],
		'pageswithprop' => [],
		'prefixsearch' => [],
		'protectedtitles' => [],
		'querypage' => [],
		'random' => [],
		'recentchanges' => [],
		'search' => [],
		'usercontribs' => [],
		'watchlist' => [],
		'watchlistraw' => [],
	];

	/**
	 * Modules that provide a 'searchinfo.totalhits' value which needs adjusting.
	 */
	private const MODULES_WITH_TOTALHITS = [ 'search' ];

	/**
	 * Indexed tag names used by each list module in XML output.
	 */
	private const INDEXED_TAG_NAMES = [
		'allpages' => 'p',
		'alllinks' => 'l',
		'allfileusages' => 'f',
		'allredirects' => 'r',
		'alltransclusions' => 't',
		'allimages' => 'img',
		'backlinks' => 'bl',
		'embeddedin' => 'ei',
		'imageusage' => 'iu',
		'categorymembers' => 'cm',
		'exturlusage' => 'eu',
		'iwbacklinks' => 'iw',
		'langbacklinks' => 'lb',
		'logevents' => 'item',
		'pageswithprop' => 'page',
		'prefixsearch' => 'ps',
		'protectedtitles' => 'pt',
		'querypage' => 'page',
		'random' => 'page',
		'recentchanges' => 'rc',
		'search' => 'p',
		'usercontribs' => 'item',
		'watchlist' => 'item',
		'watchlistraw' => 'wr',
	];

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

	/**
	 * Filters page titles from API query list results that the current user
	 * cannot read. This fires after module execution but before the result
	 * is sent to the client.
	 *
	 * Because we operate on already-paginated result sets, any adjusted totals
	 * are estimates only.
	 *
	 * @inheritDoc
	 */
	public function onAPIAfterExecute( $module ) {
		if ( !( $module instanceof ApiQuery ) ) {
			return;
		}

		$user = $this->getRelevantUser();
		if ( !$user ) {
			return;
		}

		$result = $module->getResult();
		$queryData = $result->getResultData( [ 'query' ] );
		if ( !$queryData || !is_array( $queryData ) ) {
			return;
		}

		foreach ( self::LIST_MODULE_CONFIG as $moduleName => $config ) {
			if ( !isset( $queryData[$moduleName] ) || !is_array( $queryData[$moduleName] ) ) {
				continue;
			}

			$this->filterListModule( $result, $moduleName, $config, $queryData[$moduleName], $user );
		}
	}

	/**
	 * @param ApiResult $result
	 * @param string $moduleName
	 * @param array $config
	 * @param array $items
	 * @param User $user
	 */
	private function filterListModule(
		ApiResult $result,
		string $moduleName,
		array $config,
		array $items,
		User $user
	): void {
		$titleField = $config['titleField'] ?? 'title';
		$nsField = $config['nsField'] ?? 'ns';

		$removedCount = 0;
		$filteredItems = [];

		foreach ( $items as $key => $item ) {
			if ( !is_array( $item ) ) {
				continue;
			}
			if ( !isset( $item[$titleField] ) ) {
				$filteredItems[$key] = $item;
				continue;
			}

			$title = $this->resolveTitleFromResultRow( $item, $titleField, $nsField );
			if ( !$title ) {
				$filteredItems[$key] = $item;
				continue;
			}

			if ( $this->permissionManager->userCan( 'read', $user, $title ) ) {
				$filteredItems[$key] = $item;
			} else {
				$removedCount++;
			}
		}

		if ( $removedCount === 0 ) {
			return;
		}

		$result->removeValue( 'query', $moduleName );
		$reindexed = array_values( $filteredItems );
		foreach ( $reindexed as $item ) {
			$result->addValue( [ 'query', $moduleName ], null, $item );
		}
		$result->addIndexedTagName(
			[ 'query', $moduleName ],
			self::INDEXED_TAG_NAMES[$moduleName] ?? 'item'
		);

		if ( in_array( $moduleName, self::MODULES_WITH_TOTALHITS ) ) {
			$this->adjustTotalHits( $result, $moduleName, $removedCount );
		}
	}

	/**
	 * Resolve a Title object from a result row.
	 *
	 * @param array $item
	 * @param string $titleField
	 * @param string|null $nsField
	 * @return \MediaWiki\Title\Title|null
	 */
	private function resolveTitleFromResultRow( array $item, string $titleField, ?string $nsField ) {
		$titleValue = $item[$titleField];

		if ( $nsField !== null && isset( $item[$nsField] ) ) {
			return $this->titleFactory->makeTitle( (int)$item[$nsField], $titleValue );
		}

		return $this->titleFactory->newFromText( $titleValue );
	}

	/**
	 * Adjust the totalhits value in searchinfo.
	 * This is an estimate — we only know how many were removed from the
	 * current page of results, not from the full result set.
	 *
	 * @param ApiResult $result
	 * @param string $moduleName
	 * @param int $removedCount
	 */
	private function adjustTotalHits( ApiResult $result, string $moduleName, int $removedCount ): void {
		$searchInfo = $result->getResultData( [ 'query', $moduleName . 'info' ] );
		if ( !$searchInfo || !isset( $searchInfo['totalhits'] ) ) {
			$searchInfo = $result->getResultData( [ 'query', 'searchinfo' ] );
		}

		if ( !$searchInfo || !isset( $searchInfo['totalhits'] ) ) {
			return;
		}

		$adjustedTotal = max( 0, (int)$searchInfo['totalhits'] - $removedCount );
		$result->addValue(
			[ 'query', 'searchinfo' ],
			'totalhits',
			$adjustedTotal,
			ApiResult::OVERRIDE
		);
	}
}
