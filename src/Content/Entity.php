<?php
namespace BlueSpice\Content;

use BlueSpice\Entity as EntityBase;
use MediaWiki\Content\JsonContent;
use MediaWiki\Json\FormatJson;
use MediaWiki\MediaWikiServices;
use MediaWiki\Xml\Xml;

class Entity extends JsonContent {

	/**
	 * @param string $text
	 * @param string $modelId
	 */
	public function __construct( $text, $modelId = '' ) {
		parent::__construct( $text, $modelId );
	}

	/**
	 * Decodes the JSON into a PHP associative array.
	 *
	 * @return array
	 */
	public function getJsonData() {
		return FormatJson::decode( $this->getText(), true );
	}

	/**
	 * @return bool Whether content is valid JSON.
	 */
	public function isValid() {
		return $this->getJsonData() !== null;
	}

	/**
	 * Pretty-print JSON
	 *
	 * @return bool|null|string
	 */
	public function beautifyJSON() {
		$decoded = FormatJson::decode( $this->getText(), true );
		if ( !is_array( $decoded ) ) {
			return null;
		}
		return FormatJson::encode( $decoded, true );
	}

	/**
	 * Constructs an HTML representation of a JSON object.
	 *
	 * @param array $mapping
	 * @return string HTML
	 */
	protected function objectTable( $mapping ) {
		$rows = [];

		foreach ( $mapping as $key => $val ) {
			$rows[] = $this->objectRow( $key, $val );
		}
		return Xml::tags( 'table', [ 'class' => 'mw-json' ],
			Xml::tags( 'tbody', [], implode( "\n", $rows ) )
		);
	}

	/**
	 * Constructs HTML representation of a single key-value pair.
	 *
	 * @param string $key
	 * @param mixed $val
	 * @return string HTML.
	 */
	protected function objectRow( $key, $val ) {
		$th = Xml::elementClean( 'th', [], $key );
		if ( is_array( $val ) ) {
			$td = Xml::tags( 'td', [], self::objectTable( $val ) );
		} else {
			if ( is_string( $val ) ) {
				$val = '"' . $val . '"';
			} else {
				$val = FormatJson::encode( $val );
			}

			$td = Xml::elementClean( 'td', [ 'class' => 'value' ], $val );
		}

		return Xml::tags( 'tr', [], $th . $td );
	}

	/**
	 * Returns a generated id for a given entity.
	 *
	 * @param EntityBase $entity
	 * @return int
	 */
	public static function generateID( EntityBase $entity ) {
		// this is the case if the current Entity is new (no Title created yet)
		// Get the page_title of the last created title in entity namespace and
		// add +1. Entities are stored like: MYEntityNamespace:1,
		// MYEntityNamespace:2, MYEntityNamespace:3
		$id = (int)$entity->get( EntityBase::ATTR_ID, 0 );
		if ( $id > 0 ) {
			return $id;
		}
		$dbr = MediaWikiServices::getInstance()->getDBLoadBalancer()
			->getConnection( DB_REPLICA );
		$res = $dbr->selectRow(
			'page',
			'page_title',
			[ 'page_namespace' => $entity::NS ],
			__METHOD__,
			[
				'ORDER BY' => 'LENGTH( page_title ) DESC, page_title DESC'
			]
		);

		if ( $res ) {
			$id = (int)$res->page_title + 1;
		} else {
			$id = 1;
		}
		return $id;
	}
}
