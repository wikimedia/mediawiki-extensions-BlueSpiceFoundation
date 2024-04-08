<?php

require_once __DIR__ . '/BSMaintenance.php';

class DetectCategoryRecursion extends BSMaintenance {

	/** @var array */
	private $dupes = [];

	public function execute() {
		$categoriesRes = $this->getDB( DB_REPLICA )->select( 'category', 'cat_title', [], __METHOD__ );

		foreach ( $categoriesRes as $row ) {
			$this->getSubCategoriesFromPath( [ $row->cat_title ] );
		}

		if ( empty( $this->dupes ) ) {
			$this->output( "No recursion detected\n" );
			return;
		}
		$this->output( "Recursion detected:\n" );
		foreach ( $this->dupes as $dupe ) {
			$this->output( implode( ' -> ', $dupe ) . "\n" );
		}
	}

	/**
	 * @param array $nodes
	 */
	private function getSubCategoriesFromPath( array $nodes ) {
		if ( $this->detectRecursion( $nodes ) ) {
			$dupes = $nodes;
			$dupes = array_unique( $dupes );
			sort( $dupes );
			$this->dupes[implode( '|', $dupes )] = $nodes;
			return;
		}
		$last = end( $nodes );
		$subcategories = $this->getSubCategoriesFromDB( $last );
		foreach ( $subcategories as $subcat ) {
			$this->getSubCategoriesFromPath( array_merge( $nodes, [ $subcat ] ) );
		}
	}

	/**
	 * @param array $nodes
	 *
	 * @return bool
	 */
	private function detectRecursion( array $nodes ) {
		$processed = [];
		foreach ( $nodes as $node ) {
			if ( in_array( $node, $processed ) ) {
				return true;
			}
			$processed[] = $node;
		}
		return false;
	}

	/**
	 * @param string $last
	 * @return array
	 */
	private function getSubCategoriesFromDB( string $last ): array {
		$dbr = $this->getDB( DB_REPLICA );
		$resSubCategories = $dbr->select(
			[ 'page', 'categorylinks' ],
			[ 'page_title' ],
			[ 'cl_to' => $last, 'page_namespace' => NS_CATEGORY ],
			__METHOD__,
			[ '' ],
			[ 'categorylinks' =>
				[
					'INNER JOIN', 'page_id = cl_from'
				]
			]
		);

		$subcategories = [];
		foreach ( $resSubCategories as $row ) {
			$subcategories[] = $row->page_title;
		}
		asort( $subcategories );

		return $subcategories;
	}
}

$maintClass = DetectCategoryRecursion::class;
require_once RUN_MAINTENANCE_IF_MAIN;
