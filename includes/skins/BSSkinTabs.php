<?php

/**
 * This class is designed to be more or less compatible with
 * 'Skins\Chameleon\Components\Component' from Chameleon skin
 * -- https://www.mediawiki.org/wiki/Skin:Chameleon
 */
abstract class BSSkinTabs {
	/**
	 *
	 * @var BaseTemplate
	 */
	private $mSkinTemplate;

	/**
	 *
	 * @param BaseTemplate $template
	 * @param DOMElement $domElement
	 * @param int $indent
	 */
	public function __construct( BaseTemplate $template, DOMElement $domElement = null, $indent = 0 ) {
		$this->mSkinTemplate = $template;
	}

	/**
	 *
	 * @return string HTML
	 */
	public function getHtml() {
		$aData = $this->getTabs();
		$aList = array();

		$sHeading = $this->getHeading();
		if( !empty( $sHeading ) ) {
			$aList[] = '<h2>' . $sHeading . '</h2>';
		}
		$aList[] = '<ul id="'.$this->getTabListID().'">';
		$aContents = array();
		$iCount = 0;
		foreach( $aData as $key => $item ) {
			if ( $item instanceof ViewBaseElement ) {
				$aList[] = $item->execute();
			} else {
				$aList[] = Html::rawElement(
					'li',
					array(
						'class' =>  implode( ' ',  $this->getTabListItemClasses( $key, $item, $iCount ) )
					),
					Html::rawElement(
						'a',
						array(
							'id' => $this->getTabAnchorID( $key, $item ),
							'href' => '#' . $this->getTabContentID( $key, $item ),
							'class' => implode( ' ', $this->getTabAnchorClasses( $key, $item ) ),
							'title' => $this->getTabAnchorTitle( $key, $item )
						),
						$this->getTabAnchorText( $key, $item )
					)
				);
				$content = $item['content'];
				if( $item['content'] instanceof ViewBaseElement ) {
					$content = $item['content']->execute();
				}

				$aContents[] = Html::rawElement(
					'div',
					array(
						'id' => $this->getTabContentID( $key, $item ),
						'class' => implode( ' ', $this->getTabContentClasses( $key, $item ) ),
						'style' => $this->getTabContentStyle( $key, $item, $iCount )
					),
					$content
				);
			}
			$iCount++;
		}
		$aList[] = '</ul>';

		return Html::rawElement(
			'div',
			array(
				'id' => $this->getContainerID()
			),
			implode( "\n", $aList ).
			implode( "\n", $aContents )
		);

	}

	protected function getActiveTabIndex() {
		return (int)$this->mSkinTemplate->getSkin()->getRequest()
			->getCookie(
				$this->getTabIndexCookieName(),
				null,
				0
			);
	}

	/**
	 *
	 * @return BaseTemplate
	 */
	public function getSkinTemplate() {
		return $this->mSkinTemplate;
	}

	protected abstract function getTabIndexCookieName();

	protected abstract function getTabs();

	protected abstract function getHeading();

	protected abstract function getTabListID();

	protected abstract function getContainerID();

	protected function getTabContentID( $key, $item ) {
		return $key;
	}

	protected function getTabAnchorClasses( $key, $item ) {
		return isset( $item['classes'] ) ? $item['classes'] : array();
	}

	protected function getTabContentClasses( $key, $item ) {
		return array( 'bs-tab-content' );
	}

	public function getTabAnchorTitle( $key, $item ) {
		return $item['label'];
	}

	public function getTabAnchorText( $key, $item ) {
		return $item['label'];
	}

	public function getTabAnchorID( $key, $item ) {
		return 'bs-tab-link-' . $key;
	}

	public function getTabListItemClasses( $key, $item, $iCount ) {
		return array();
	}

	public function getTabContentStyle( $key, $item, $iCount ) {
		if( $iCount !== $this->getActiveTabIndex() ) {
			return 'display:none';
		}
	}

}
