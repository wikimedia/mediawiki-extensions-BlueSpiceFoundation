<?php

namespace BlueSpice\Task\WikiPage;

use Title;
use BlueSpice\ParamProcessor\ParamDefinition;
use BlueSpice\ParamProcessor\ParamType;
use BlueSpice\Utility\WikiTextLinksHelper\InterlanguageLinksHelper;

class SetLanguageLinks extends \BlueSpice\Task {
	const PARAM_LANGUAGE_LINKS = 'languagelinks';

	/**
	 * @param array $params
	 * @return Status
	 */
	protected function doExecute() {
		$langLinks = $this->getParam( static::PARAM_LANGUAGE_LINKS );
		$wikiText = $this->fetchCurrentRevisionWikiText();
		$helper = $this->getLanguageLinksHelper( $wikiText );

		$remove = array_values( $this->filterDiffTargets(
			$helper->getTargets(),
			$langLinks
		) );

		$add = array_values( $this->filterDiffTargets(
			$langLinks,
			$helper->getTargets()
		) );

		$helper->removeTargets( $remove );
		$helper->addTargets( $add );

		return $this->saveWikiPage( $helper->getWikitext() );
	}

	/**
	 *
	 * @param Title[] $array1
	 * @param Title[] $array2
	 * @return type
	 */
	protected function filterDiffTargets( array $array1, array $array2 ) {
		return array_filter( $array1, function( Title $e ) use( $array2 ) {
			foreach( $array2 as $target ) {
				if( $e->equals( $target ) ) {
					return false;
				}
			}
			return true;
		});
	}

	/**
	 *
	 * @param string $wikitext
	 * @return Status
	 */
	protected function saveWikiPage( $wikitext ) {
		return $this->getWikiPage()->doEditContent(
			new \WikitextContent( $wikitext ),
			'',
			0,
			false,
			$this->context->getUser()
		);
	}

	/**
	 *
	 * @param string $wikiText
	 * @return InterlanguageLinksHelper
	 */
	protected function getLanguageLinksHelper( $wikiText ) {
		return $this->getServices()->getBSUtilityFactory()
			->getWikiTextLinksHelper( $wikiText )->getLanguageLinksHelper();
	}

	/**
	 *
	 * @return string
	 * @throws Exception
	 */
	protected function fetchCurrentRevisionWikiText() {
		$content = $this->getWikiPage()->getContent();
		if( $content instanceof \WikitextContent === false ) {
			throw new Exception(
				"Can not set wikitext-language-links on non-wikitext content"
			);
		}
		return $content->getNativeData();
	}

	/**
	 *
	 * @return \WikiPage
	 */
	protected function getWikiPage() {
		return $this->context->getWikiPage();
	}

	/**
	 *
	 * @return ParamDefinition[]
	 */
	public function getArgsDefinitions() {
		$langLinks = new \BSTitleListParam(
			ParamType::TITLE_LIST,
			static::PARAM_LANGUAGE_LINKS,
			[],
			null,
			true
		);

		$validator = new \BSTitleValidator();
		$validator->setOptions( [ 'hastoexist' => false ] );
		$langLinks->setValueValidator( $validator );

		return [
			$langLinks
		];
	}

	/**
	 *
	 * @return string[]
	 */
	public function getTaskPermissions() {
		return [ 'edit' ];
	}

}
