<?php

namespace BlueSpice\Special;

use MediaWiki\Html\Html;
use MediaWiki\Json\FormatJson;

class Credits extends \BlueSpice\SpecialPage {

	private $aTranslators = [];

	public function __construct() {
		parent::__construct( 'SpecialCredits' );
	}

	/**
	 *
	 * @param string $par
	 */
	public function execute( $par ) {
		parent::execute( $par );

		$html = '';
		$html .= $this->renderOpenTable( [
			$this->msg( 'bs-credits-programmers' )->text(),
			$this->msg( 'bs-credits-dnt' )->text(),
			$this->msg( 'bs-credits-contributors' )->text()
		] );
		$html .= Html::openElement( 'tr', [ 'style' => 'vertical-align: top;' ] );
		$configNames = [
			'CreditsProgrammers',
			'CreditsDesignAndTesting',
			'CreditsContributors'
		];
		foreach ( $configNames as $cfgName ) {
			$html .= Html::openElement( 'td' );
			$html .= $this->renderNameList(
				$this->getConfig()->get( $cfgName )
			);
			$html .= Html::closeElement( 'td' );
		}
		$html .= Html::closeElement( 'tr' );
		$html .= Html::closeElement( 'table' );

		$html .= $this->renderOpenTable( [
			$this->msg( 'bs-credits-translators' )->text(),
			$this->msg( 'bs-credits-translation' )->text(),
		] );
		$html .= Html::openElement( 'tr', [ 'style' => 'vertical-align: top;' ] );
		$html .= Html::openElement( 'td' );
		$html .= $this->renderNameList(
			$this->getTranslatorsList()
		);
		$html .= Html::closeElement( 'td' );
		$html .= Html::openElement( 'td' );
		$html .= $this->renderNameList(
			$this->getConfig()->get( 'CreditsTranslation' )
		);
		$html .= Html::closeElement( 'td' );
		$html .= Html::closeElement( 'tr' );
		$html .= Html::closeElement( 'table' );
		$this->getOutput()->addHTML( $html );
	}

	/**
	 *
	 * @param array $headElements
	 * @param string $html
	 * @return string
	 */
	protected function renderOpenTable( $headElements, $html = '' ) {
		$html .= Html::openElement( 'table', [
			'class' => 'wikitable',
			'style' => 'width:100%',
		] );
		$html .= Html::openElement( 'tr' );
		foreach ( $headElements as $content ) {
			$html .= Html::element( 'th', [], $content );
		}
		$html .= Html::closeElement( 'tr' );
		return $html;
	}

	/**
	 *
	 * @param array $list
	 * @param string $hmtl
	 * @return string
	 */
	protected function renderNameList( $list, $hmtl = '' ) {
		$hmtl .= Html::openElement( 'ul' );
		foreach ( $list as $entry ) {
			$hmtl .= Html::element( 'li', [], $entry );
		}
		$hmtl .= Html::closeElement( 'ul' );
		return $hmtl;
	}

	/**
	 *
	 * @return array
	 */
	protected function getTranslatorsList() {
		$cacheHelper = $this->services->getService( 'BSUtilityFactory' )
			->getCacheHelper();
		$key = $cacheHelper->getCacheKey(
			'BlueSpice',
			'Credits',
			'Translators'
		);
		$translators = $cacheHelper->get( $key );

		if ( $translators !== false ) {
			wfDebugLog(
				'bluespice',
				__CLASS__ . ': Fetching translators from cache'
			);
		} else {
			wfDebugLog(
				'bluespice',
				__CLASS__ . ': Fetching translators from DB'
			);
			$translators = $this->generateTranslatorsList();
			// Keep list for one day
			$cacheHelper->set( $key, $translators, 86400 );
		}
		return $translators;
	}

	protected function generateTranslatorsList() {
		$aPaths = [
			$this->getConfig()->get( 'ExtensionDirectory' ),
			$this->getConfig()->get( 'StyleDirectory' ),
		];

		$translators = [];
		foreach ( $aPaths as $sPath ) {
			$this->readInTranslators( $sPath, $translators );
		}

		$translators = array_map( 'trim', $translators );
		$translators = array_unique( $translators );
		asort( $translators );
		return $translators;
	}

	/**
	 *
	 * @param string $dir
	 * @param array &$translators
	 */
	protected function readInTranslators( $dir, &$translators = [] ) {
		$iterator = new \RegexIterator(
			new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator( $dir )
			),
			'/^.+\.json/i'
		);

		foreach ( $iterator as $fileinfo ) {
			if ( !$fileinfo->isFile() ) {
				continue;
			}

			$filepath = \BsFileSystemHelper::normalizePath(
				$fileinfo->getPathname()
			);
			if ( $filepath === null ) {
				continue;
			}
			if ( strpos( $filepath, '/i18n/' ) === false ) {
				continue;
			}
			if ( strpos( $filepath, '/BlueSpice' ) === false ) {
				continue;
			}

			$content = FormatJson::decode( file_get_contents(
				$fileinfo->getPathname()
			), true );

			if ( !is_array( $content ) ) {
				continue;
			}

			$this->readInTranslatorsFile( $content, $translators );
		}
	}

	/**
	 *
	 * @param array $content
	 * @param array &$translators
	 */
	protected function readInTranslatorsFile( $content, &$translators ) {
		foreach ( $content as $data ) {
			if ( !$data instanceof \stdClass || empty( $data->authors ) ) {
				continue;
			}
			if ( is_string( $data->authors ) ) {
				$data->authors = [ $data->authors ];
			}
			if ( !is_array( $data->authors ) ) {
				continue;
			}

			foreach ( $data->authors as $author ) {
				if ( !is_string( $author ) ) {
					continue;
				}
				$author = strip_tags( $author );
				$translators[] = $author;
			}
		}
	}
}
