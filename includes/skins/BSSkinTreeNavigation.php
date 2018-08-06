<?php

/**
 * This class is designed to be more or less compatible with
 * 'Skins\Chameleon\Components\Component' from Chameleon skin
 * -- https://www.mediawiki.org/wiki/Skin:Chameleon
 */
abstract class BSSkinTreeNavigation {
	/**
	 *
	 * @var BaseTemplate
	 */
	private $mSkinTemplate;

	/**
	 *
	 * @param BaseTemplate $template
	 * @param DOMElement|null $domElement
	 * @param int $indent
	 */
	public function __construct( BaseTemplate $template, DOMElement $domElement = null, $indent = 0 ) {
		$this->mSkinTemplate = $template;
	}

	/**
	 *
	 * @return BaseTemplate
	 */
	public function getSkinTemplate() {
		return $this->mSkinTemplate;
	}

	protected abstract function getContainerID();

	/**
	 *
	 * @return string HTML
	 */
	public function getHtml() {
		$this->mHtml = '';

		return Html::rawElement(
			'div',
			array(
				'id' => $this->getContainerID(),
			),
			$this->renderTree()
		);
	}

	/**
	 * @return string[] the resource loader modules needed by this component
	 */
	public function getResourceLoaderModules() {
		return array(
			'ext.bluespice.skin.treenavigation'
		);
	}

	protected function renderTree() {
		$root = $this->makeTreeRootNode();
		$renderer = $this->makeTreeRenderer( $root );

		$paths = $this->getPathsToExpand();
		foreach( $paths as $path ) {
			$renderer->expandPath( $path );
		}

		return $renderer->render();
	}

	/**
	 * @return BSTreeNode
	 */
	abstract protected function makeTreeRootNode();

	/**
	 *
	 * @param BSTreeNode $root
	 * @return \BSTreeRenderer
	 */
	protected function makeTreeRenderer( $root ) {
		return new BSTreeRenderer( $root, new HashConfig([
			BSTreeRenderer::CONFIG_ID => $this->getTreeId()
		]) );
	}

	protected function getTreeId() {
		return $this->getContainerID() . '-tree';
	}

	protected function getPathsToExpand() {
		$webRequest = $this->getSkinTemplate()->getSkin()->getRequest();
		$cookie = $webRequest->getCookie( $this->getTreeId() );

		$paths = FormatJson::decode( $cookie );

		return is_array( $paths ) ? $paths : [];
	}

}
