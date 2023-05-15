<?php

require_once __DIR__ . '/BSMaintenance.php';

/**
 * Maintenance script to check deleted images directory for filenames matching current images.
 */
class CheckDuplicateImages extends BSMaintenance {

	public function __construct() {
		parent::__construct();
		$this->addDescription( 'Check deleted images directory for filenames matching current images' );
	}

	public function execute() {
		$currentImages = $this->getCurrentImages();
		$deletedImages = $this->getDeletedImages();

		$duplicateImages = array_intersect( $currentImages, $deletedImages );

		$output = "Duplicate images:\n";
		$output .= implode( "\n", $duplicateImages );

		$this->output( $output );
	}

	/**
	 * @return array $imageNames
	 */
	public function getCurrentImages() {
		$dbr = $this->getDB( DB_REPLICA );

			$res = $dbr->select(
			'image',
			'img_name',
			'',
			__METHOD__
		);

		$imageNames = [];
		foreach ( $res as $row ) {
			$imageNames[] = $row->img_name;
		}

		return $imageNames;
	}

	/**
	 * @return array
	 */
	public function getDeletedImages(): array {
		// phpcs:ignore MediaWiki.NamingConventions.ValidGlobalName.allowedPrefix
		global $IP, $wgScriptPath, $wgResourceBasePath;

		$basePath = str_replace( $wgScriptPath, '', $IP );
		$deletedImagesPath = $basePath . $wgResourceBasePath . '/images/deleted';

		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $deletedImagesPath, RecursiveDirectoryIterator::SKIP_DOTS ),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		$fileNames = [];
		foreach ( $iterator as $file ) {
			if ( $file->isFile() ) {
				$filename = basename( $file->getPathname() );
				$fileNames[] = pathinfo( $filename, PATHINFO_FILENAME );
			}
		}

		return $this->resolveDeletedImages( $fileNames );
	}

	/**
	 * @param array $fileNames
	 * @return array $resolvedNames
	 */
	public function resolveDeletedImages( array $fileNames ): array {
		$dbr = $this->getDB( DB_REPLICA );

		$res = $dbr->select(
			'filearchive',
			'fa_name',
			[
				'fa_sha1' => $fileNames
			],
			__METHOD__
		);

		$resolvedNames = [];
		foreach ( $res as $row ) {
			$resolvedNames[] = $row->fa_name;
		}

		return $resolvedNames;
	}
}

$maintClass = CheckDuplicateImages::class;
require_once RUN_MAINTENANCE_IF_MAIN;
