<?php
/**
 * Permanently deletes very old revisions of pages.
 *
 * The current revision of a page and every revision that has been approved via
 * ContentStabilization (listed in `stable_points`) are never deleted.
 *
 * Example:
 * php purgeVeryOldRevisions.php --titlepattern=Old.*? --sfr=abc --deletebefore=20160301 --dry-run
 *
 * @file
 * @ingroup Maintenance
 * @license GPL-3.0-only
 */

use MediaWiki\Maintenance\Maintenance;
use MediaWiki\Title\Title;
use Wikimedia\Rdbms\IDatabase;
use Wikimedia\Rdbms\IMaintainableDatabase;
use Wikimedia\Rdbms\IReadableDatabase;
use Wikimedia\Timestamp\ConvertibleTimestamp;
use Wikimedia\Timestamp\TimestampFormat;

require_once __DIR__ . '/../../../maintenance/Maintenance.php';

class PurgeVeryOldRevisions extends Maintenance {

	private const DEFAULT_MAX_AGE_YEARS = 10;

	/** @var string TS_MW */
	private $cutoff;

	/** @var int[] */
	private $namespaces = [];

	/** @var string[] Category DB keys */
	private $categories = [];

	/** @var string|null */
	private $titleRegex = null;

	/** @var bool */
	private $dryRun = false;

	/** @var bool|null */
	private $hasStablePoints = null;

	/** @var int */
	private $totalPages = 0;

	/** @var int */
	private $totalRevisions = 0;

	public function __construct() {
		parent::__construct();
		$this->addDescription(
			'Permanently deletes revisions older than a given date. The current revision of a page ' .
			'and revisions approved via ContentStabilization are always kept. This cannot be undone! ' .
			'In a wiki farm, use --sfr=<instance> to select the subwiki.'
		);
		$this->addOption( 'deletebefore',
			'Delete revisions created before this timestamp. Format: YYYYMMDD or YYYYMMDDHHMMSS. ' .
			'Default: ' . self::DEFAULT_MAX_AGE_YEARS . ' years ago', false, true );
		$this->addOption( 'namespace',
			'Comma-separated list of namespace IDs or names', false, true );
		$this->addOption( 'category',
			'Comma-separated list of categories (without namespace prefix)', false, true );
		$this->addOption( 'titlepattern',
			'Regular expression matched against the page title (without namespace prefix)', false, true );
		$this->addOption( 'dry-run', 'Only report what would be deleted' );
		$this->setBatchSize( 100 );
	}

	public function execute() {
		$this->cutoff = $this->parseCutoff( $this->getOption( 'deletebefore' ) );
		$this->namespaces = $this->parseNamespaces( $this->getOption( 'namespace', '' ) );
		$this->categories = $this->parseCategories( $this->getOption( 'category', '' ) );
		$this->titleRegex = $this->parseTitlePattern( $this->getOption( 'titlepattern' ) );
		$this->dryRun = $this->hasOption( 'dry-run' );

		$this->output( 'Deleting revisions before ' .
			ConvertibleTimestamp::convert( TimestampFormat::ISO_8601, $this->cutoff ) . "\n" );
		if ( $this->dryRun ) {
			$this->output( "DRY RUN - nothing will be deleted\n" );
		}

		$dbr = $this->getReplicaDB();
		$lastPageId = 0;
		do {
			$rows = $this->newPageQuery( $dbr, $lastPageId )->fetchResultSet();
			foreach ( $rows as $row ) {
				$lastPageId = (int)$row->page_id;
				if ( $this->titleRegex && !preg_match( $this->titleRegex, strtr( $row->page_title, '_', ' ' ) ) ) {
					continue;
				}
				$this->processPage( $row );
			}
		} while ( $rows->numRows() >= $this->getBatchSize() );

		$verb = $this->dryRun ? 'Would delete' : 'Deleted';
		$this->output( "$verb {$this->totalRevisions} old revision(s) on {$this->totalPages} page(s). " .
			"Pages themselves are never deleted.\n" );
	}

	/**
	 * @param string|null $value
	 * @return string TS_MW
	 */
	private function parseCutoff( ?string $value ): string {
		if ( $value === null ) {
			$date = new DateTime();
			$date->modify( '-' . self::DEFAULT_MAX_AGE_YEARS . ' years' );
			return ConvertibleTimestamp::convert( TS_MW, $date->getTimestamp() );
		}
		if ( preg_match( '/^\d{8}$/', $value ) ) {
			$value .= '000000';
		}
		if ( !preg_match( '/^\d{14}$/', $value ) || !checkdate(
			(int)substr( $value, 4, 2 ), (int)substr( $value, 6, 2 ), (int)substr( $value, 0, 4 )
		) ) {
			$this->fatalError( "Invalid --deletebefore '$value', expected YYYYMMDD or YYYYMMDDHHMMSS" );
		}
		return $value;
	}

	/**
	 * @param string $value
	 * @return int[]
	 */
	private function parseNamespaces( string $value ): array {
		$services = $this->getServiceContainer();
		$contLang = $services->getContentLanguage();
		$nsInfo = $services->getNamespaceInfo();

		$namespaces = [];
		foreach ( $this->splitList( $value ) as $item ) {
			if ( ctype_digit( $item ) ) {
				$index = (int)$item;
			} else {
				$index = $contLang->getNsIndex( $item );
				if ( $index === false ) {
					$index = $nsInfo->getCanonicalIndex( strtolower( $item ) );
				}
			}
			if ( $index === false || $index === null || !$nsInfo->exists( $index ) ) {
				$this->fatalError( "Unknown namespace '$item'" );
			}
			$namespaces[] = $index;
		}
		return array_unique( $namespaces );
	}

	/**
	 * @param string $value
	 * @return string[]
	 */
	private function parseCategories( string $value ): array {
		$categories = [];
		foreach ( $this->splitList( $value ) as $item ) {
			$title = Title::makeTitleSafe( NS_CATEGORY, $item );
			if ( !$title ) {
				$this->fatalError( "Invalid category '$item'" );
			}
			$categories[] = $title->getDBkey();
		}
		return array_unique( $categories );
	}

	/**
	 * @param string|null $pattern
	 * @return string|null
	 */
	private function parseTitlePattern( ?string $pattern ): ?string {
		if ( $pattern === null || $pattern === '' ) {
			return null;
		}
		$regex = '/' . str_replace( '/', '\/', $pattern ) . '/u';
		// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		if ( @preg_match( $regex, '' ) === false ) {
			$this->fatalError( "Invalid --titlepattern '$pattern'" );
		}
		return $regex;
	}

	/**
	 * @param string $value
	 * @return string[]
	 */
	private function splitList( string $value ): array {
		return array_values( array_filter( array_map( 'trim', explode( ',', $value ) ), 'strlen' ) );
	}

	/**
	 * @param IReadableDatabase $dbr
	 * @param int $lastPageId
	 * @return \Wikimedia\Rdbms\SelectQueryBuilder
	 */
	private function newPageQuery( IReadableDatabase $dbr, int $lastPageId ) {
		$query = $dbr->newSelectQueryBuilder()
			->select( [ 'page_id', 'page_namespace', 'page_title', 'page_latest' ] )
			->from( 'page' )
			->where( $dbr->expr( 'page_id', '>', $lastPageId ) )
			->orderBy( 'page_id' )
			->limit( $this->getBatchSize() )
			->caller( __METHOD__ );

		if ( $this->namespaces ) {
			$query->andWhere( [ 'page_namespace' => $this->namespaces ] );
		}
		if ( $this->categories ) {
			$query->join( 'categorylinks', null, 'cl_from = page_id' )
				->join( 'linktarget', null, 'lt_id = cl_target_id' )
				->andWhere( [ 'lt_namespace' => NS_CATEGORY, 'lt_title' => $this->categories ] )
				->distinct();
		}
		return $query;
	}

	/**
	 * @param stdClass $row
	 */
	private function processPage( $row ) {
		$revIds = $this->getPurgeableRevisionIds( (int)$row->page_id, (int)$row->page_latest );
		if ( !$revIds ) {
			return;
		}

		$title = Title::makeTitle( (int)$row->page_namespace, $row->page_title );
		$count = count( $revIds );
		$this->totalPages++;
		$this->totalRevisions += $count;
		$verb = $this->dryRun ? 'would delete' : 'deleting';
		$this->output( "{$title->getPrefixedText()}: $verb $count old revision(s), " .
			"keeping current revision {$row->page_latest}\n" );

		if ( $this->dryRun ) {
			return;
		}
		foreach ( array_chunk( $revIds, $this->getBatchSize() ) as $batch ) {
			$this->purgeRevisions( $batch );
		}
		$title->invalidateCache();
	}

	/**
	 * @param int $pageId
	 * @param int $latestRevId
	 * @return int[]
	 */
	private function getPurgeableRevisionIds( int $pageId, int $latestRevId ): array {
		$dbr = $this->getReplicaDB();
		$conds = [
			'rev_page' => $pageId,
			$dbr->expr( 'rev_timestamp', '<', $dbr->timestamp( $this->cutoff ) ),
			$dbr->expr( 'rev_id', '!=', $latestRevId ),
		];
		$stableRevIds = $this->getStableRevisionIds( $dbr, $pageId );
		if ( $stableRevIds ) {
			$conds[] = $dbr->expr( 'rev_id', '!=', $stableRevIds );
		}

		return array_map( 'intval', $dbr->newSelectQueryBuilder()
			->select( 'rev_id' )
			->from( 'revision' )
			->where( $conds )
			->orderBy( 'rev_id' )
			->caller( __METHOD__ )
			->fetchFieldValues() );
	}

	/**
	 * Approved revisions (ContentStabilization) must never be deleted
	 *
	 * @param IReadableDatabase $dbr
	 * @param int $pageId
	 * @return int[]
	 */
	private function getStableRevisionIds( IReadableDatabase $dbr, int $pageId ): array {
		if ( $this->hasStablePoints === null ) {
			if ( !( $dbr instanceof IMaintainableDatabase ) ) {
				return [];
			}
			$this->hasStablePoints = $dbr->tableExists( 'stable_points', __METHOD__ );
		}
		if ( !$this->hasStablePoints ) {
			return [];
		}
		return array_map( 'intval', $dbr->newSelectQueryBuilder()
			->select( 'sp_revision' )
			->from( 'stable_points' )
			->where( [ 'sp_page' => $pageId ] )
			->caller( __METHOD__ )
			->fetchFieldValues() );
	}

	/**
	 * @param int[] $revIds
	 */
	private function purgeRevisions( array $revIds ) {
		$dbw = $this->getPrimaryDB();
		$this->beginTransactionRound( __METHOD__ );

		$contentIds = $dbw->newSelectQueryBuilder()
			->select( 'slot_content_id' )
			->distinct()
			->from( 'slots' )
			->where( [ 'slot_revision_id' => $revIds ] )
			->caller( __METHOD__ )
			->fetchFieldValues();
		$this->delete( $dbw, 'slots', [ 'slot_revision_id' => $revIds ] );
		$this->purgeOrphanedContent( $dbw, $contentIds );

		$this->delete( $dbw, 'change_tag', [ 'ct_rev_id' => $revIds ] );
		$this->delete( $dbw, 'ip_changes', [ 'ipc_rev_id' => $revIds ] );
		$this->delete( $dbw, 'recentchanges', [ 'rc_this_oldid' => $revIds ] );
		$this->delete( $dbw, 'revision', [ 'rev_id' => $revIds ] );

		$dbw->newUpdateQueryBuilder()
			->update( 'revision' )
			->set( [ 'rev_parent_id' => 0 ] )
			->where( [ 'rev_parent_id' => $revIds ] )
			->caller( __METHOD__ )
			->execute();

		$this->commitTransactionRound( __METHOD__ );
	}

	/**
	 * Content rows (and their text blobs) may be shared between revisions,
	 * e.g. after a revert, so only delete those no longer referenced.
	 *
	 * @param IDatabase $dbw
	 * @param array $contentIds
	 */
	private function purgeOrphanedContent( IDatabase $dbw, array $contentIds ) {
		if ( !$contentIds ) {
			return;
		}
		$stillUsed = $dbw->newSelectQueryBuilder()
			->select( 'slot_content_id' )
			->distinct()
			->from( 'slots' )
			->where( [ 'slot_content_id' => $contentIds ] )
			->caller( __METHOD__ )
			->fetchFieldValues();
		$orphaned = array_values( array_diff( $contentIds, $stillUsed ) );
		if ( !$orphaned ) {
			return;
		}

		$addresses = $dbw->newSelectQueryBuilder()
			->select( 'content_address' )
			->distinct()
			->from( 'content' )
			->where( [ 'content_id' => $orphaned ] )
			->caller( __METHOD__ )
			->fetchFieldValues();
		$this->delete( $dbw, 'content', [ 'content_id' => $orphaned ] );
		if ( !$addresses ) {
			return;
		}

		$stillUsedAddresses = $dbw->newSelectQueryBuilder()
			->select( 'content_address' )
			->distinct()
			->from( 'content' )
			->where( [ 'content_address' => $addresses ] )
			->caller( __METHOD__ )
			->fetchFieldValues();

		$textIds = [];
		foreach ( array_diff( $addresses, $stillUsedAddresses ) as $address ) {
			// Blobs in external storage ("es:") are not touched
			if ( preg_match( '/^tt:(\d+)$/', $address, $matches ) ) {
				$textIds[] = (int)$matches[1];
			}
		}
		if ( $textIds ) {
			$this->delete( $dbw, 'text', [ 'old_id' => $textIds ] );
		}
	}

	/**
	 * @param IDatabase $dbw
	 * @param string $table
	 * @param array $conds
	 */
	private function delete( IDatabase $dbw, string $table, array $conds ) {
		$dbw->newDeleteQueryBuilder()
			->deleteFrom( $table )
			->where( $conds )
			->caller( __METHOD__ )
			->execute();
	}
}

$maintClass = PurgeVeryOldRevisions::class;
require_once RUN_MAINTENANCE_IF_MAIN;
