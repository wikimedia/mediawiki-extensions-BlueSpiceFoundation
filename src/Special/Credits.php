<?php

namespace BlueSpice\Special;

class Credits extends \BlueSpice\SpecialPage {

	private $aTranslators = array();

	public function __construct() {
		parent::__construct( 'SpecialCredits' );
	}

	public function execute( $par ) {
		parent::execute( $par );

		$html = '';
		$html .= $this->renderOpenTable( [
			$this->msg( 'bs-credits-programmers' )->plain(),
			$this->msg( 'bs-credits-dnt' )->plain(),
			$this->msg( 'bs-credits-contributors' )->plain()
		]);
		$html .= \Html::openElement( 'tr', [ 'style' => 'vertical-align: top;' ] );
		$configNames = [
			'CreditsProgrammers',
			'CreditsDesignAndTesting',
			'CreditsContributors'
		];
		foreach( $configNames as $cfgName ) {
			$html .= \Html::openElement( 'td' );
			$html .= $this->renderNameList(
				$this->getConfig()->get( $cfgName )
			);
			$html .= \Html::closeElement( 'td' );
		}
		$html .= \Html::closeElement( 'tr' );
		$html .= \Html::closeElement( 'table' );

		$html .= $this->renderOpenTable( [
			$this->msg( 'bs-credits-translators' )->plain(),
			$this->msg( 'bs-credits-translation' )->plain(),
		]);
		$html .= \Html::openElement( 'tr', [ 'style' => 'vertical-align: top;' ] );
		$html .= \Html::openElement( 'td' );
		$html .= $this->renderNameList(
			$this->getTranslatorsList()
		);
		$html .= \Html::closeElement( 'td' );
		$html .= \Html::openElement( 'td' );
		$html .= $this->renderNameList(
			$this->getConfig()->get( 'CreditsTranslation' )
		);
		$html .= \Html::closeElement( 'td' );
		$html .= \Html::closeElement( 'tr' );
		$html .= \Html::closeElement( 'table' );
		$this->getOutput()->addHTML( $html );
	}

	protected function renderOpenTable( $headElements, $html = '' ) {
		$html .= \Html::openElement( 'table', [
			'class' => 'wikitable',
			'style' => 'width:100%',
		]);
		$html .= \Html::openElement( 'tr' );
		foreach( $headElements as $content ) {
			$html .= \Html::element( 'th', [], $content );
		}
		$html .= \Html::closeElement( 'tr' );
		return $html;
	}

	protected function renderNameList( $list, $hmtl = '' ) {
		$hmtl .= \Html::openElement( 'ul' );
		foreach( $list as $entry ) {
			$hmtl .= \Html::element( 'li', [], $entry );
		}
		$hmtl .= \Html::closeElement( 'ul' );
		return $hmtl;
	}

	protected function getTranslatorsList() {
		$key = \BsCacheHelper::getCacheKey(
			'BlueSpice',
			'Credits',
			'Translators'
		);
		$translators = \BsCacheHelper::get( $key );

		if ( $translators ) {
			wfDebugLog(
				'BsMemcached',
				__CLASS__ . ': Fetching translators from cache'
			);
		} else {
			wfDebugLog(
				'BsMemcached',
				__CLASS__ . ': Fetching translators from DB'
			);
			$this->generateTranslatorsList();
			// Keep list for one day
			\BsCacheHelper::set( $key, $translators, 86400 );
		}
		return $translators;
	}

	protected function generateTranslatorsList() {
		global $IP;
		$aPaths = array(
			$IP . '/extensions/',
			$IP . '/skins/'
		);

		$translators = [];
		foreach ( $aPaths as $sPath ) {
			$this->readInTranslators( $sPath, $translators );
		}

		$translators = array_map( 'trim', $translators );
		$translators = array_unique( $translators );
		asort( $translators );
		return $translators;
	}

	protected function readInTranslators( $dir, &$translators = [] ) {
		$iterator = new \RegexIterator(
			new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator( $dir )
			),
			'/^.+\.json/i'
		);

		foreach ( $iterator as $fileinfo ) {
			if( !$fileinfo->isFile() ) {
				continue;
			}

			$filepath = \BsFileSystemHelper::normalizePath(
				$fileinfo->getPathname()
			);
			if ( strpos( $filepath, '/i18n/' ) === false ) {
				continue;
			}
			if ( strpos( $filepath, '/BlueSpice' ) === false ) {
				continue;
			}

			$content = \FormatJson::decode( file_get_contents(
				$fileinfo->getPathname()
			));
			$this->readInTranslatorsFile( $content, $translators );
		}
	}

	protected function readInTranslatorsFile( $content, &$translators ) {
		foreach ( $content as $data ) {
			if ( !$data instanceof \stdClass || empty( $data->authors ) ) {
				continue;
			}

			foreach ( $data->authors as $author ) {
				if( !is_string( $author ) ) {
					continue;
				}
				$author = strip_tags( $author );
				$translators[] = $author;
			}
		}
	}
}
