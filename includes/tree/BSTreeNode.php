<?php

use MediaWiki\Config\Config;
use MediaWiki\Config\HashConfig;
use MediaWiki\Config\MultiConfig;

class BSTreeNode {

	public const CONFIG_TEXT = 'text';
	public const CONFIG_CHILDNODES = 'childNodes';
	public const CONFIG_IS_LEAF = 'isLeaf';
	public const CONFIG_EXPANDED = 'expanded';
	public const CONFIG_EXPANDABLE = 'expandable';
	public const CONFIG_ACTIVE = 'active';

	/**
	 *
	 * @var int|string
	 */
	protected $id = '';

	/**
	 *
	 * @var string
	 */
	protected $text = '';

	/**
	 *
	 * @var BSTreeNode[]
	 */
	protected $childNodes = [];

	/**
	 *
	 * @var bool
	 */
	protected $isLeaf = false;

	/**
	 *
	 * @var bool
	 */
	protected $expanded = false;

	/**
	 *
	 * @var bool
	 */
	protected $active = false;

	/**
	 *
	 * @var BSTreeNode
	 */
	protected $parentNode = null;

	/**
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 *
	 * @var Config
	 */
	protected $config = null;

	/**
	 *
	 * @param int|string $id
	 * @param BSTreeNode|null $parentNode
	 * @param Config $config
	 */
	public function __construct( $id, $parentNode, $config ) {
		$this->id = $id;
		$this->parentNode = $parentNode;

		$config = new MultiConfig( [
			$config,
			new HashConfig( [
				self::CONFIG_CHILDNODES => [],
				self::CONFIG_IS_LEAF => false,
				self::CONFIG_EXPANDED => false,
				self::CONFIG_EXPANDABLE => true,
				self::CONFIG_ACTIVE	=> false
			] )
		] );

		$this->config = $config;

		// Init primary fields from config
		$this->text = $config->get( self::CONFIG_TEXT );
		$this->childNodes = $config->get( self::CONFIG_CHILDNODES );
		$this->isLeaf = $config->get( self::CONFIG_IS_LEAF );
		$this->expanded = $config->get( self::CONFIG_EXPANDED );
		$this->active = $config->get( self::CONFIG_ACTIVE );
	}

	/**
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get( $key, $default ) {
		if ( $this->config->has( $key ) ) {
			return $this->config->get( $key );
		}

		return $default;
	}

	/**
	 *
	 * @return \BSTreeNode[]
	 */
	public function getChildNodes() {
		return $this->childNodes;
	}

	/**
	 *
	 * @return bool
	 */
	public function hasChildNodes() {
		return !$this->isLeaf && count( $this->childNodes ) > 0;
	}

	/**
	 *
	 * @param BSTreeNode $node
	 * @return BSTreeNode
	 */
	public function appendChild( $node ) {
		$this->childNodes[] = $node;
		return $node;
	}

	/**
	 *
	 * @return mixed int|string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 *
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 *
	 * @return bool
	 */
	public function isExpanded() {
		return $this->expanded;
	}

	/**
	 *
	 * @return bool
	 */
	public function isActive() {
		return $this->active;
	}

	/**
	 *
	 * @param bool $value
	 */
	public function setActive( $value ) {
		$this->active = $value;
	}

	/**
	 *
	 * @return string
	 */
	public function getPath() {
		$basePath = '';
		if ( $this->parentNode instanceof BSTreeNode ) {
			$basePath = $this->parentNode->getPath();
		}

		return $basePath . '/' . $this->getId();
	}

	/**
	 *
	 * @return bool
	 */
	public function isRoot() {
		return $this->parentNode === null;
	}

	/**
	 * @return void
	 */
	public function expand() {
		if ( $this->get( self::CONFIG_EXPANDABLE, true ) ) {
			$this->expanded = true;
		} else {
			throw new MWException( "Node {$this->getPath()} can not be expanded!" );
		}
	}

	/**
	 * @return void
	 */
	public function collapse() {
		if ( $this->get( self::CONFIG_EXPANDABLE, true ) ) {
			$this->expanded = false;
		} else {
			throw new MWException( "Node {$this->getPath()} can not be collapsed!" );
		}
	}

	/**
	 *
	 * @return bool
	 */
	public function isExpandable() {
		return $this->get( self::CONFIG_EXPANDABLE, true );
	}

	/**
	 *
	 * @return bool
	 */
	public function isCollapsible() {
		return $this->isExpandable();
	}

	/**
	 *
	 * @return bool
	 */
	public function isLeaf() {
		return $this->get( self::CONFIG_IS_LEAF, false );
	}

	/**
	 *
	 * @return BSTreeNode|null
	 */
	public function getParentNode() {
		return $this->parentNode;
	}

}
