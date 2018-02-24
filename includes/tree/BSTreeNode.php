<?php

class BSTreeNode {

	const CONFIG_TEXT = 'text';
	const CONFIG_CHILDNODES = 'childNodes';
	const CONFIG_IS_LEAF = 'isLeaf';
	const CONFIG_EXPANDED = 'expanded';
	const CONFIG_EXPANDABLE = 'expandable';

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
	 * @var boolean
	 */
	protected $isLeaf = false;

	/**
	 *
	 * @var boolean
	 */
	protected $expanded = false;

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
	 * @param $id int|string
	 * @param BSTreeNode|null $parentNode
	 * @param Config $config
	 */
	public function __construct( $id, $parentNode, $config ) {
		$this->id = $id;
		$this->parentNode = $parentNode;

		$config = new MultiConfig([
			$config,
			new HashConfig([
				self::CONFIG_CHILDNODES => [],
				self::CONFIG_IS_LEAF => false,
				self::CONFIG_EXPANDED => false,
				self::CONFIG_EXPANDABLE => true,
			])
		]);

		$this->config = $config;

		//Init primary fields from config
		$this->text = $config->get( self::CONFIG_TEXT );
		$this->childNodes = $config->get( self::CONFIG_CHILDNODES );
		$this->isLeaf = $config->get( self::CONFIG_IS_LEAF );
		$this->expanded = $config->get( self::CONFIG_EXPANDED );
	}

	public function get( $key, $default ) {
		if( $this->config->has( $key ) ) {
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
	 * @return boolean
	 */
	public function isExpanded() {
		return $this->expanded;
	}

	/**
	 *
	 * @return string
	 */
	public function getPath() {
		$basePath = '';
		if( $this->parentNode instanceof BSTreeNode ) {
			$basePath = $this->parentNode->getPath();
		}

		return $basePath . '/' .$this->getId();
	}

	/**
	 *
	 * @return boolean
	 */
	public function isRoot() {
		return $this->parentNode === null;
	}

	/**
	 * @return void
	 */
	public function expand() {
		if( $this->get( self::CONFIG_EXPANDABLE, true ) ) {
			$this->expanded = true;
		}
		else {
			throw new MWException( "Node {$this->getPath()} can not be expanded!" );
		}
	}

	/**
	 * @return void
	 */
	public function collapse() {
		if( $this->get( self::CONFIG_EXPANDABLE, true ) ) {
			$this->expanded = false;
		}
		else {
			throw new MWException( "Node {$this->getPath()} can not be collapsed!" );
		}
	}

	/**
	 *
	 * @return boolean
	 */
	public function isExpandable() {
		return $this->get( self::CONFIG_EXPANDABLE, true );
	}

	/**
	 *
	 * @return boolean
	 */
	public function isCollapsible() {
		return $this->isExpandable();
	}

	/**
	 *
	 * @return boolean
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
