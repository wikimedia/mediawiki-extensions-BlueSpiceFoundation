<?php

namespace BlueSpice\HookHandler;

use DOMDocument;
use DOMElement;

class PDFExportHandler {
	/**
	 * Add bootstrap icons resources to pdf export
	 *
	 * @param array &$template
	 * @param array &$contents
	 * @param ExportSpecification $specification
	 * @param array &$page
	 * @return bool
	 */
	public function onBSUEModulePDFBeforeAddingContent( &$template, &$contents, $specification, &$page ) {
		/** @var DOMDocument */
		$dom = $template['dom'];

		/** @var DOMNodeList */
		$head = $dom->getElementsByTagName( 'head' );

		$dir = dirname( __DIR__, 2 );

		// entypo
		$this->addTemplateResource( "entypo.ttf", "$dir/resources/entypo/entypo.ttf", $template );
		$this->addTemplateResource( "entypo.css", "$dir/resources/entypo/entypo.css", $template );
		$this->addTemplateResource( "entypo-fonts.css", "$dir/resources/entypo/entypo-pdf-integration.css", $template );
		$this->addTemlateResourceLinkNodes( 'stylesheets/entypo.css', $head->item( 0 ) );
		$this->addTemlateResourceLinkNodes( 'stylesheets/entypo-fonts.css', $head->item( 0 ) );

		// fontawesome
		$this->addTemplateResource( "fontawesome.ttf", "$dir/resources/fontawesome/fontawesome.ttf", $template );
		$this->addTemplateResource( "fontawesome.css", "$dir/resources/fontawesome/fontawesome.css", $template );
		$this->addTemplateResource( "fontawesome-fonts.css",
			"$dir/resources/fontawesome/fontawesome-pdf-integration.css", $template );
		$this->addTemlateResourceLinkNodes( 'stylesheets/fontawesome.css', $head->item( 0 ) );
		$this->addTemlateResourceLinkNodes( 'stylesheets/fontawesome-fonts.css', $head->item( 0 ) );

		// icomoon
		$this->addTemplateResource( "icomoon.ttf", "$dir/resources/icomoon/icomoon.ttf", $template );
		$this->addTemplateResource( "icomoon.css", "$dir/resources/icomoon/icomoon.css", $template );
		$this->addTemplateResource(
			"icomoon-fonts.css", "$dir/resources/icomoon/icomoon-pdf-integration.css", $template );
		$this->addTemlateResourceLinkNodes( 'stylesheets/icomoon.css', $head->item( 0 ) );
		$this->addTemlateResourceLinkNodes( 'stylesheets/icomoon-fonts.css', $head->item( 0 ) );

		return true;
	}

	/**
	 * @param string $name
	 * @param string $path
	 * @param array &$template
	 * @param string $key
	 * @return void
	 */
	private function addTemplateResource( string $name, string $path, array &$template, $key = 'STYLESHEET' ) {
		$template['resources'][$key][$name] = $path;
	}

	/**
	 * @param string $href
	 * @param DOMElement $parent
	 * @return void
	 */
	private function addTemlateResourceLinkNodes( string $href, DOMElement $parent ) {
		/** @var DOMElement */
		$linkNode = $this->createLinkNode( $parent->ownerDocument, $href );
		$parent->appendChild( $linkNode );
	}

	/**
	 * @param DOMDocument $dom
	 * @param string $href
	 * @param string $type
	 * @param string $rel
	 * @return DOMElement
	 */
	private function createLinkNode(
		DOMDocument $dom, string $href, string $type = 'text/css', string $rel = 'stylesheet'
	): DOMElement {
		/** @var DOMElement */
		$linkNode = $dom->createElement( 'link' );
		$linkNode->setAttribute( 'href', $href );
		$linkNode->setAttribute( 'type', $type );
		$linkNode->setAttribute( 'rel', $rel );
		return $linkNode;
	}
}
