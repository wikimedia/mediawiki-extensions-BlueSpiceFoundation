<?php

namespace BlueSpice\Utility;

use MediaWiki\Category\TrackingCategories;
use MediaWiki\Linker\LinkTarget;

/**
 * Helper to recognize and filter out MediaWiki tracking categories
 *
 * Tracking categories are added to pages by the parser (or by extensions) to flag
 * technical conditions - they are not editorial categories and therefore should not
 * be shown to the user in places like the page footer or the search results.
 */
class TrackingCategoryHelper {

	/**
	 * @var TrackingCategories
	 */
	private $trackingCategories;

	/**
	 * DB keys of all known tracking categories, used as a lookup map
	 *
	 * @var array|null
	 */
	private $dbKeys = null;

	/**
	 * @param TrackingCategories $trackingCategories
	 */
	public function __construct( TrackingCategories $trackingCategories ) {
		$this->trackingCategories = $trackingCategories;
	}

	/**
	 * @param string|LinkTarget $category Category name, with or without namespace prefix,
	 *  in DB key or in text form
	 * @return bool
	 */
	public function isTrackingCategory( $category ): bool {
		$dbKey = $this->normalize( $category );
		if ( $dbKey === '' ) {
			return false;
		}

		return isset( $this->getTrackingCategoryDBKeys()[$dbKey] );
	}

	/**
	 * Removes all tracking categories from a list of categories
	 *
	 * Keys are not preserved.
	 *
	 * @param array $categories List of category names or LinkTargets
	 * @return array
	 */
	public function filterOut( array $categories ): array {
		return array_values( array_filter(
			$categories,
			function ( $category ) {
				return !$this->isTrackingCategory( $category );
			}
		) );
	}

	/**
	 * @return array DB keys of all known tracking categories as keys, `true` as value
	 */
	public function getTrackingCategoryDBKeys(): array {
		if ( $this->dbKeys === null ) {
			$this->dbKeys = [];
			foreach ( $this->trackingCategories->getTrackingCategories() as $trackingCategory ) {
				foreach ( $trackingCategory['cats'] as $cat ) {
					$this->dbKeys[$cat->getDBkey()] = true;
				}
			}
		}

		return $this->dbKeys;
	}

	/**
	 * @param string|LinkTarget $category
	 * @return string
	 */
	private function normalize( $category ): string {
		if ( $category instanceof LinkTarget ) {
			return $category->getDBkey();
		}
		if ( !is_string( $category ) ) {
			return '';
		}

		$category = str_replace( ' ', '_', trim( $category ) );
		// Category names may or may not carry the namespace prefix
		$prefixes = [ 'Category:', 'category:' ];
		foreach ( $prefixes as $prefix ) {
			if ( strpos( $category, $prefix ) === 0 ) {
				$category = substr( $category, strlen( $prefix ) );
				break;
			}
		}

		return ucfirst( $category );
	}
}
