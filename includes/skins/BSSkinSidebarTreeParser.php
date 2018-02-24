<?php

class BSSkinSidebarTreeParser {

	/**
	 *
	 * @var BaseTemplate
	 */
	protected $skinTemplate = null;

	/**
	 *
	 * @var string
	 */
	protected $wikiTextSource = '';

	/**
	 *
	 * @var string
	 */
	protected $rootNodeId = 'SIDEBAR';

	/**
	 *
	 * @param BaseTemplate $skinTemplate
	 * @param string $wikiText
	 */
	public function __construct( $skinTemplate, $wikiText = '', $rootNodeId = 'SIDEBAR' ) {
		$this->skinTemplate = $skinTemplate;
		$this->wikiTextSource = $wikiText;
		if( empty( $this->wikiTextSource ) ) {
			$this->wikiTextSource = wfMessage( 'sidebar' )->plain();
		}
		$this->rootNodeId = $rootNodeId;
	}

	/**
	 *
	 * @return \BSTreeNode
	 */
	public function parse() {
		$html = $this->parseWikiText();
		$dom = new DOMDocument();
		$dom->loadHTML( "<html><body>$html</body></html>" );

		$root = new BSTreeNode( $this->rootNodeId, null, new HashConfig([
			BSTreeNode::CONFIG_TEXT => $this->rootNodeId
		]) );
		$this->convertDOMNodeAndAppendTreeNode(
			$dom->getElementsByTagName( 'body' )->item( 0 ),
			$root
		);

		return $root;
	}

	/**
	 *
	 * @param DOMElement $domNode
	 * @param BSTreeNode $parentNode
	 */
	protected function convertDOMNodeAndAppendTreeNode( $domNode, $parentNode ) {
		$domUL = $domNode->getElementsByTagName( 'ul' )->item( 0 );
		if( $domUL instanceof DOMElement && $domUL->childNodes->length > 0 ) {
			foreach( $domUL->childNodes as $domLI ) {
				if( $domLI instanceof DOMElement === false || strtolower( $domLI->nodeName ) !== 'li' ) {
					continue;
				}
				$nodeValue = $this->getNodeValue( $domLI );
				$config = $this->makeConfigFromNodeValue( $nodeValue );

				//Permission check!
				if( $config->has( 'targetTitle' ) ) {
					if( !$config->get( 'targetTitle' )->userCan( 'read' ) ) {
						//We skip all childnodes!
						continue;
					}
				}

				$childNode = $this->makeNode( $parentNode, $config );
				$parentNode->appendChild( $childNode );

				$this->convertDOMNodeAndAppendTreeNode( $domLI, $childNode );
			}
		}
	}

	/**
	 * ATTENTION: $domNode->nodeValue would contain all values of descendant nodes!
	 * @param DOMElement $domNode
	 * @return string
	 */
	protected function getNodeValue( $domNode  ) {
		$nodeValue = '';
		foreach( $domNode->childNodes as $node ) {
			if( $node instanceof DOMText === false ) {

				continue;
			}
			$nodeValue .= $node->nodeValue;
		}
		return trim( $nodeValue );
	}

	protected function makeConfigFromNodeValue( $nodeValue ) {
		$parts = explode( '|', $nodeValue );
		$target = $parts[0];
		$text = $target;

		if( isset( $parts[1] ) ) {
			$text = $parts[1];
		}

		$targetMsg = wfMessage( $target );
		if( $targetMsg->exists() ) {
			$target = $targetMsg->plain();
		}

		$textMsg = wfMessage( $text );
		if( $textMsg->exists() ) {
			$text = $textMsg->plain();
		}

		$iconUrl = '';
		$iconCls = '';
		if( isset( $parts[2] ) ) {
			$text = $parts[2];
		}

		//TODO: Implement all this icons stuff!
		$cfg = [
			'id' => Sanitizer::escapeId( $parts[0] ),
			BSTreeNode::CONFIG_TEXT => $text
		];

		$targetTitle = Title::newFromText( $target );
		if( $targetTitle instanceof Title ) {
			$cfg['targetTitle'] = $targetTitle;
			$cfg['html'] = Linker::link( $targetTitle, $text );
		}

		return new HashConfig( $cfg );
	}

	/**
	 *
	 * @param \BSTreeNode $parentNode
	 * @param \Config $config
	 * @return \BSTreeNode
	 */
	protected function makeNode($parentNode, $config) {
		if( $config->get('id') === 'navigation' && $parentNode->isRoot() ) {
			$config = new MultiConfig( [
				new HashConfig([
					BSTreeNode::CONFIG_EXPANDED => true
				]),
				$config
			] );
		}

		return new BSTreeNode( $config->get( 'id' ), $parentNode, $config );
	}

	protected function parseWikiText() {
		$params = new DerivativeRequest(
			$this->skinTemplate->getSkin()->getRequest(),
			array(
				'action' => 'parse',
				'text' => $this->wikiTextSource,
				'title' => $this->skinTemplate->getSkin()->getTitle()->getPrefixedDBkey(),
			)
		);

		$api = new ApiMain( $params );
		$api->execute();
		$data = $api->getResult()->getResultData();

		return $data['parse']['text'];
	}
}
