<?php

namespace BlueSpice\Api;

use BlueSpice\Api;
use MediaWiki\Json\FormatJson;
use MediaWiki\Title\Title;
use Wikimedia\ParamValidator\ParamValidator;

class Linker extends Api {
	/**
	 * @var array
	 */
	private $links = [];

	/**
	 * @var array
	 */
	private $linkDescs = [];

	/**
	 * @inheritDoc
	 */
	public function execute() {
		$this->readInLinkDescs();
		$this->renderLinks();
		$this->returnResult();
	}

	/**
	 * @inheritDoc
	 */
	protected function getAllowedParams() {
		return parent::getAllowedParams() + [
			'linkdescs' => [
				ParamValidator::PARAM_REQUIRED => true,
				ParamValidator::PARAM_TYPE => 'string',
				static::PARAM_HELP_MSG => 'apihelp-bs-linker-param-linkdescs',
			]
		];
	}

	private function readInLinkDescs() {
		$params = trim( $this->getParameter( 'linkdescs' ) );
		$this->linkDescs = FormatJson::decode( $params, true );
	}

	private function renderLinks() {
		$linkRenderer = $this->services->getLinkRenderer();

		foreach ( $this->linkDescs as $id => $linkDesc ) {
			// Compare `LinkRenderer::makeLink` signature
			$fullLinkDesc = $linkDesc + [
				'target' => '',
				'text' => null,
				'attribs' => [],
				'query' => []
			];

			$target = Title::newFromText( $fullLinkDesc['target'] );
			if ( $target === null ) {
				continue;
			}

			// We do not check `$fullLinkDesc['attribs']`,
			// as it can be an empty string or `null`

			if ( !is_array( $fullLinkDesc['attribs'] ) ) {
				$fullLinkDesc['attribs'] = [];
			}

			if ( !is_array( $fullLinkDesc['query'] ) ) {
				$fullLinkDesc['query'] = [];
			}

			$this->links[$id] = $linkRenderer->makeLink(
				$target,
				$fullLinkDesc['text'],
				$fullLinkDesc['attribs'],
				$fullLinkDesc['query']
			);
		}
	}

	private function returnResult() {
		$result = $this->getResult();
		$result->addValue( null, 'links', $this->links );
	}
}
