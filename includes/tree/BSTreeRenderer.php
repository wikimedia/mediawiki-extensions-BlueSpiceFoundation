<?php

use MediaWiki\Config\Config;
use MediaWiki\Config\HashConfig;
use MediaWiki\Config\MultiConfig;
use MediaWiki\Html\Html;
use MediaWiki\Json\FormatJson;
use MediaWiki\Parser\Sanitizer;

class BSTreeRenderer {

	public const CONFIG_ID = 'id';
	public const CONFIG_ROOT_VISIBLE = 'rootVisible';

	/**
	 *
	 * @var BSTreeNode
	 */
	protected $root = null;

	/**
	 * @var Config
	 */
	protected $config = null;

	/**
	 * The output buffer
	 * @var string
	 */
	protected $html = '';

	/**
	 *
	 * @param BSTreeNode $root
	 * @param Config|null $config
	 */
	public function __construct( $root, $config = null ) {
		$this->root = $root;

		$configs = [
			new HashConfig( [
				self::CONFIG_ROOT_VISIBLE => false
			] )
		];

		if ( $config instanceof Config ) {
			array_unshift( $configs, $config );
		}

		$this->config = new MultiConfig( $configs );
	}

	public function render() {
		$this->clearBuffer();
		$nodes = $this->root->getChildNodes();
		if ( $this->config->get( self::CONFIG_ROOT_VISIBLE ) ) {
			$nodes = [ $this->root ];
		}

		$this->renderHead();
		$this->renderNodeItems( $nodes, 0 );
		$this->renderFoot();

		return $this->html;
	}

	/**
	 *
	 * @param BSTreeNode[] $nodes
	 * @param int $level
	 */
	protected function renderNodeItems( $nodes, $level ) {
		foreach ( $nodes as $node ) {
			$this->renderNodeItem( $node, $level );
		}
	}

	/**
	 *
	 * @param string $path
	 */
	public function expandPath( $path ) {
		$path = explode( '/', $path );
		// We need to remove the trailing empty element as a path starts with '/'
		array_shift( $path );
		$this->doExpandPath( $path, $this->root );
	}

	protected function clearBuffer() {
		$this->html = '';
	}

	/**
	 *
	 * @param array $path
	 * @param BSTreeNode $node
	 */
	protected function doExpandPath( $path, $node ) {
		$firstElement = array_shift( $path );
		if ( Sanitizer::escapeIdForAttribute( $node->getId() ) !== $firstElement ) {
			if ( $firstElement !== '' || $node->isRoot() ) {
				return;
			}
		}

		$node->expand();

		foreach ( $node->getChildNodes() as $childNode ) {
			$this->doExpandPath( $path, $childNode );
		}
	}

	protected function renderHead() {
		$this->html .= Html::openElement(
			'ul',
			[
				'id' => Sanitizer::escapeIdForAttribute( $this->config->get( self::CONFIG_ID ) ),
				'class' => 'bs-tree-root'
			]
		);
	}

	public function renderFoot() {
		$this->html .= Html::closeElement( 'ul' );
	}

	/**
	 *
	 * @param BSTreeNode $node
	 * @param int $level
	 */
	protected function renderNodeItem( $node, $level ) {
		$attibs = [
			'class' => $this->makeNodeItemClass( $node, $level ),
			'data-bs-nodedata' => $this->makeNodeItemData( $node )
		];
		if ( $node->isExpandable() && $node->hasChildNodes() ) {
			$attibs = array_merge( $attibs, [ 'aria-haspopup' => 'true' ] );
			$attibs = array_merge( $attibs, [ 'aria-expanded' => 'false' ] );
			$attibs = array_merge( $attibs, [ 'role' => 'treeitem' ] );
		}
		if ( $node->isExpanded() ) {
			$attibs = array_merge( $attibs, [ 'aria-expanded' => 'true' ] );
		}

		$this->html .= Html::openElement(
			'li',
			$attibs
		);
		$this->html .= Html::openElement( 'div' );
		$this->html .= Html::element( 'span', [ 'class' => 'bs-icon' ] );
		$this->renderNode( $node );
		$this->html .= Html::closeElement( 'div' );
		if ( $node->hasChildNodes() ) {
			$this->html .= Html::openElement( 'ul' );
			$this->renderNodeItems( $node->getChildNodes(), $level + 1 );
			$this->html .= Html::closeElement( 'ul' );
		}
		$this->html .= Html::closeElement( 'li' );
	}

	/**
	 *
	 * @param int $level
	 */
	protected function renderIndent( $level ) {
		for ( $i = 0; $i < $level; $i++ ) {
			$this->html .= Html::element( 'span', [ 'class' => 'bs-treeindent' ] );
		}
	}

	/**
	 *
	 * @param BSTreeNode $node
	 */
	public function renderNode( $node ) {
		$this->html .= Html::openElement( 'span', [ 'class' => 'bs-treenode-value' ] );
		$this->html .= $node->get( 'html', $node->getText() );
		$this->html .= Html::closeElement( 'span' );
	}

	/**
	 *
	 * @param BSTreeNode $node
	 * @param int $level
	 * @return string
	 */
	protected function makeNodeItemClass( $node, $level ) {
		$classes = [
			'bs-treenodeitem',
			'bs-tree-level-' . $level,
			$node->isActive() ? 'active' : ''
		];

		return implode( ' ', $classes );
	}

	/**
	 *
	 * @param BSTreeNode $node
	 * @return string
	 */
	protected function makeNodeItemData( $node ) {
		return FormatJson::encode( [
			'id' => $node->getId(),
			'path' => $node->getPath()
		] );
	}
}
