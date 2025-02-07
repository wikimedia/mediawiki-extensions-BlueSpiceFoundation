<?php

namespace BlueSpice\Html\Descriptor;

use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Language\RawMessage;
use MediaWiki\Message\Message;
use MediaWiki\Title\Title;

class TitleLink extends LinkBase {

	public const CONFIG_LABELTYPE = 'TitleLink_LabelType';

	public const LABEL_BASENAME = 'basename';
	public const LABEL_UNPREFIXED = 'unprefixed';

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Title|null $title
	 */
	public function __construct( $context, $config, $title = null ) {
		parent::__construct( $context, $config );

		$this->title = $title;
		if ( $this->title === null ) {
			$this->title = $context->getTitle();
		}
	}

	/**
	 *
	 * @return Message
	 */
	public function getLabel() {
		$label = $this->title->getPrefixedText();
		if ( $this->config->has( static::CONFIG_LABELTYPE ) ) {
			$labelType = $this->config->get( static::CONFIG_LABELTYPE );
			if ( $label === static::LABEL_BASENAME ) {
				$label = $this->title->getBaseText();
			}
			if ( $label === static::LABEL_UNPREFIXED ) {
				$label = $this->title->getText();
			}
		}

		return new RawMessage( $label );
	}

	/**
	 *
	 * @return Message
	 */
	public function getTooltip() {
		return new RawMessage( $this->title->getPrefixedText() );
	}

	/**
	 *
	 * @return string[]
	 */
	public function getCSSClasses() {
		$classes = parent::getCSSClasses();
		if ( !$this->title->isExternal() && !$this->title->exists() ) {
			$classes[] = 'new';
		}
		if ( $this->title->isExternal() ) {
			$classes[] = 'external';
		}

		return $classes;
	}

	/**
	 * See also
	 * - BlueSpice\Hook\HtmlPageLinkRendererEnd\AddDataTitle
	 * - BlueSpice\Hook\LinkerMakeMediaLinkFile\AddDataAttributes
	 * - BlueSpice\Hook\ThumbnailBeforeProduceHTML\AddDataAttributes
	 *
	 * @return array
	 */
	public function getDataAttributes(): array {
		$data = parent::getDataAttributes();

		$data['bs-title'] = $this->title->getPrefixedText();
		if ( $this->title->getNamespace() === NS_FILE ) {
			$data['bs-filename'] = $this->title->getText();
			$data['bs-filetimestamp'] = '';
		}

		return $data;
	}

	/**
	 *
	 * @return string
	 */
	public function getHref() {
		return $this->title->getLinkURL();
	}

}
