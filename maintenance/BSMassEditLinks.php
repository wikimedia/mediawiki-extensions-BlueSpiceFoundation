<?php

use MediaWiki\MediaWikiServices;
use MediaWiki\Title\TitleFactory;

require_once __DIR__ . '/BSMassEditBase.php';

class BSMassEditLinks extends BSMassEditBase {

	/**
	 * @var string
	 */
	private $summay = '';

	/**
	 * @var array
	 */
	private $titleMap = [];

	/**
	 * @var TitleFactory
	 */
	private $titleFactory = null;

	/**
	 */
	public function __construct() {
		parent::__construct();
		$this->addOption(
			'mapfile',
			'MoveBatch compatible file with a source/target title map',
			true,
			true
		);

		$this->addOption(
			'summary',
			'Edit summary',
			false,
			true
		);
	}

	/**
	 * @return void
	 */
	public function execute() {
		$this->titleFactory = MediaWikiServices::getInstance()->getTitleFactory();
		$success = $this->loadMapFile();
		if ( !$success ) {
			return;
		}

		$this->summay = $this->getOption( 'summary' );
		parent::execute();
	}

	/**
	 * @return bool
	 */
	private function loadMapFile(): bool {
		$filename = $this->getOption( 'mapfile' );
		if ( !$filename ) {
			$this->error( 'No mapfile given!' );
			return false;
		}
		if ( !file_exists( $filename ) ) {
			$this->error( "Mapfile '$filename' does not exist!" );
			return false;
		}
		$content = file_get_contents( $filename );
		$lines = explode( "\n", $content );
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( !$line ) {
				continue;
			}
			$parts = explode( '|', $line, 2 );
			if ( count( $parts ) !== 2 ) {
				$this->error( "Invalid line '$line' in mapfile '$filename'!" );
				return false;
			}
			$source = trim( $parts[0] );
			$target = trim( $parts[1] );
			if ( !$source || !$target ) {
				$this->error( "Invalid line '$line' in mapfile '$filename'!" );
				return false;
			}

			$source = $this->titleFactory->newFromText( $source );
			$target = $this->titleFactory->newFromText( $target );
			if ( !$source || !$target ) {
				$this->error( "Invalid line '$line' in mapfile '$filename'!" );
				return false;
			}

			$sourceKey = $source->getPrefixedDBkey();
			$targetKey = $target->getPrefixedDBkey();
			$this->titleMap[$sourceKey] = $targetKey;
		}

		return true;
	}

	/**
	 * @inheritDoc
	 */
	public function textModificationCallback( $matches ) {
		$parts = explode( '|', $matches[1], 2 );
		$target = $parts[0];
		$target = trim( $target );
		$targetTitle = $this->titleFactory->newFromText( $target );
		$targetKey = $targetTitle->getPrefixedDBkey();

		$isFileLink = $targetTitle->getNamespace() === NS_FILE;
		$isCategoryLink = $targetTitle->getNamespace() === NS_CATEGORY;

		if ( !isset( $this->titleMap[$targetKey] ) ) {
			return $matches[0];
		}
		$parts[0] = $this->titleMap[$targetKey];
		if (
			!$isFileLink
			&& !$isCategoryLink
			&& count( $parts ) === 1
			&& $target !== $parts[0]
		) {
			$parts[] = $target;
		}

		return '[[' . implode( '|', $parts ) . ']]';
	}

	/**
	 * @inheritDoc
	 */
	protected function getEditSummay( $wikiPage ) {
		if ( $this->summay ) {
			return $this->summay;
		}
		return parent::getEditSummay( $wikiPage );
	}
}

$maintClass = BSMassEditLinks::class;
require_once RUN_MAINTENANCE_IF_MAIN;
