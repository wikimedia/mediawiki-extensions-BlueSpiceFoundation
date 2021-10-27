<?php

namespace BlueSpice\Data\Categorylinks;

use BsNamespaceHelper;
use Content;
use MWNamespace;
use Title;
use WikiPage;

class SecondaryDataProvider extends \BlueSpice\Data\SecondaryDataProvider {

	/**
	 *
	 * @var \MediaWiki\Linker\LinkRenderer
	 */
	protected $linkrenderer = null;

	/**
	 *
	 * @param \MediaWiki\Linker\LinkRenderer $linkrenderer
	 */
	public function __construct( $linkrenderer ) {
		$this->linkrenderer = $linkrenderer;
	}

	/**
	 *
	 * @param Record &$dataSet
	 */
	protected function doExtend( &$dataSet ) {
		$categoryPage = Title::newFromText( $dataSet->get( Record::CATEGORY_TITLE ), NS_CATEGORY );

		$dataSet->set(
			Record::CATEGORY_LINK,
			$this->linkrenderer->makeLink( $categoryPage, $categoryPage->getText() )
		);

		// is explicit category?
		$dataSet->set(
			Record::CATEGORY_IS_EXPLICIT,
			false
		);

		$title = Title::newFromID( $dataSet->get( Record::PAGE_ID ) );
		$wikiPage = WikiPage::factory( $title );

		if ( $wikiPage->getContentModel() === CONTENT_MODEL_WIKITEXT ) {
			$wikiPageContent = $wikiPage->getContent();

			if ( $wikiPageContent instanceof Content ) {
				$text = $wikiPageContent->getNativeData();

				$categoryCanonicalNSName = MWNamespace::getCanonicalName( NS_CATEGORY );
				$categoryLocalNSName = BsNamespaceHelper::getNamespaceName( NS_CATEGORY );
				$preg_pattern = "#\[\[($categoryCanonicalNSName|$categoryLocalNSName):(.*?)(\|(.*?)|)\]\]#si";
				$matches = [];
				$status = preg_match_all( $preg_pattern, $text, $matches, PREG_PATTERN_ORDER );

				foreach ( $matches[2] as $match ) {
					$categoryName = $dataSet->get( Record::CATEGORY_TITLE );
					$categoryName = ucfirst( str_replace( ' ', '_', $categoryName ) );
					$match = ucfirst( str_replace( ' ', '_', trim( $match ) ) );
					if ( $match === $categoryName ) {
						$dataSet->set(
							Record::CATEGORY_IS_EXPLICIT,
							true
						);
					}
				}
			}
		}
	}
}
