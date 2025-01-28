<?php

namespace BlueSpice\Task\WikiPage;

use BlueSpice\ParamProcessor\ParamDefinition;
use BlueSpice\ParamProcessor\ParamType;
use BlueSpice\Utility\WikiTextLinksHelper\InterlanguageLinksHelper;
use MediaWiki\Status\Status;
use MediaWiki\Title\Title;

class SetLanguageLinks extends \BlueSpice\Task\WikiPage {
	public const PARAM_LANGUAGE_LINKS = 'languagelinks';

	/**
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
	 * @return Title[]
	 */
	protected function filterDiffTargets( array $array1, array $array2 ) {
		return array_filter( $array1, static function ( Title $e ) use( $array2 ) {
			foreach ( $array2 as $target ) {
				if ( $e->equals( $target ) ) {
					return false;
				}
			}
			return true;
		} );
	}

	/**
	 *
	 * @param string $wikiText
	 * @return InterlanguageLinksHelper
	 */
	protected function getLanguageLinksHelper( $wikiText ) {
		return $this->getServices()->getService( 'BSUtilityFactory' )
			->getWikiTextLinksHelper( $wikiText )->getLanguageLinksHelper();
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
