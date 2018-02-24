<?php
/**
 * Special page for BlueSpice credits
 *
 * Part of BlueSpice MediaWiki
 *
 * @author     Stephan Muggli <muggli@hallowelt.com>
 * @package    BlueSpice_Credits
 * @subpackage Credits
 * @copyright  Copyright (C) 2016 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v3
 * @filesource
 */

class SpecialCredits extends BsSpecialPage {

	private $aTranslators = array();

	public function __construct() {
		parent::__construct( 'SpecialCredits' );
	}

	public function execute( $par ) {
		parent::execute( $par );
		$aProgrammers = array(
			'Markus Glaser', 'Radovan Kubani', 'Sebastian Ulbricht', 'Marc Reymann',
			'Mathias Scheer', 'Thomas Lorenz', 'Tobias Weichart', 'Robert Vogel',
			'Erwin Forster', 'Karl Waldmannstetter', 'Daniel Lynge', 'Tobias Davids',
			'Patric Wirth', 'Stephan Muggli', 'Stefan Widmann', 'Jan Göttlich',
			'Benedikt Hofmann', 'Daniel Vogel', 'Leonid Verhovskij', 'Gerhard Diller',
			'Mannfred Dennerlein', 'Dejan Savuljesku', 'Josef Konrad'
		);
		$aDesignAndTesting = array(
			'Anja Ebersbach', 'Richard Heigl', 'Nathalie Köpff', 'Michael Rödl',
			'Michael Scherm', 'Dardan Diugan', 'Christina Glaser', 'Christian Graf',
			'Angelika Müller',  'Karl Skodnik', 'Astrid Scheffler', 'Sabine Gürtler',
			'Thomas Schnakenberg', 'Sabrina Dürr', 'Tobias Kornprobst', 'Luisa Roth'
		);
		$aContributors = array(
			'Aaron Schulz', 'addshore', 'Albert221', 'Amir Sarabadani', 'amritsreekumar',
			'Antoine Musso', 'Aude', 'Brad Jorsch', 'Chad Horohoe', 'Christian Aistleitner',
			'Demon', 'Florian', 'Florianschmidtwelzow', 'Frederic Mohr','Gergő Tisza',
			'Justin Du', 'Legoktm', 'MaxSem', 'MtDu', 'Ori Livneh', 'Paladox', 'Phantom42',
			'Purodha', 'Raimond Spekking', 'Reedy', 'rlot', 'Rohitt Vashishtha',
			'Siebrand Mazeland', 'Yuki Shira', 'TGC', 'Umherirrender', 'withoutaname'
		);
		$aTranslation = array(
			'Siebrand Mazeland', 'Raimond Spekking', 'Stephan Muggli'
		);

		$sLiProgrammers = '';
		foreach ( $aProgrammers as $sProgrammer ) {
			$sLiProgrammers .= '<li>' . $sProgrammer . '</li>';
		}

		$sLiDnT = '';
		foreach ( $aDesignAndTesting as $sDnT ) {
			$sLiDnT .= '<li>' . $sDnT . '</li>';
		}

		$sLiContributors = '';
		foreach ( $aContributors as $sContributor ) {
			$sLiContributors .= '<li>' . $sContributor . '</li>';
		}

		$sLiTranslation = '';
		foreach ( $aTranslation as $sTl ) {
			$sLiTranslation .= '<li>' . $sTl . '</li>';
		}

		$sOlProgrammers = '<ul>' . $sLiProgrammers . '</ul>';
		$sOlDnT = '<ul>' . $sLiDnT . '</ul>';
		$sOlContributors = '<ul>' . $sLiContributors . '</ul>';
		$sOlTl = '<ul>' . $sLiTranslation . '</ul>';

		$sKey = BsCacheHelper::getCacheKey( 'BlueSpice', 'Credits', 'Translators' );
		$aData = BsCacheHelper::get( $sKey );

		if ( $aData !== false ) {
			wfDebugLog( 'BsMemcached', __CLASS__ . ': Fetching translators from cache' );
			$this->aTranslators = $aData;
		} else {
			wfDebugLog( 'BsMemcached', __CLASS__ . ': Fetching translators from DB' );
			$this->generateTranslatorsList();
			// Keep list for one day
			BsCacheHelper::set( $sKey, $this->aTranslators, 86400 );
		}

		$sLiTranslators = '';
		foreach ( $this->aTranslators as $sTranslator ) {
			$sLiTranslators .= Html::element( 'li', array(), $sTranslator );
		}

		$sLink = '<a href="https://translatewiki.net">translatewiki.net</a>';
		$aOut = array();
		$aOut[] = '<table class="wikitable" style="width:100%">';
		$aOut[] = '<tr>';
		$aOut[] = '<th>' . wfMessage( 'bs-credits-programmers' )->plain() . '</th>';
		$aOut[] = '<th>' . wfMessage( 'bs-credits-dnt' )->plain() . '</th>';
		$aOut[] = '<th>' . wfMessage( 'bs-credits-contributors' )->plain() . '</th>';
		$aOut[] = '</tr>';
		$aOut[] = '<tr style="vertical-align: top;">';
		$aOut[] = '<td style="vertical-align: top;">' . $sOlProgrammers . '</td>';
		$aOut[] = '<td style="vertical-align: top;">' . $sOlDnT . '</td>';
		$aOut[] = '<td>' . $sOlContributors . '</td>';
		$aOut[] = '</tr>';
		$aOut[] = '</table>';

		$aOut[] = '<table class="wikitable" style="width:100%">';
		$aOut[] = '<tr>';
		$aOut[] = '<th>'. wfMessage( 'bs-credits-translators' )->plain() .'</th>';
		$aOut[] = '<th>'. wfMessage( 'bs-credits-translation' )->plain() .'</th>';
		$aOut[] = '</tr>';
		$aOut[] = '<tr>';
		$aOut[] = '<td style="vertical-align: top;">';
		$aOut[] = '<i><h6>' . wfMessage( 'bs-credits-th', $sLink )->text() . '</h6></i>';
		$aOut[] = '<ul>'. $sLiTranslators .'</ul>';
		$aOut[] = '</td>';
		$aOut[] = '<td style="vertical-align: top;">'. $sOlTl .'</td>';
		$aOut[] = '</tr>';
		$aOut[] = '</table>';

		$this->getOutput()->addHtml(implode( "\n", $aOut ) );
	}

	private function generateTranslatorsList() {
		global $IP;
		$aPaths = array(
			$IP . '/extensions/',
			$IP . '/skins/'
		);

		foreach ( $aPaths as $sPath ) {
			$this->readInTranslators( $sPath );
		}

		$this->aTranslators = array_map( 'trim', $this->aTranslators );
		$this->aTranslators = array_unique( $this->aTranslators );
		asort( $this->aTranslators );
	}

	private function readInTranslators( $sDir ) {
		$oIterator = new RegexIterator(
			new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator( $sDir )
			),
			'/^.+\.json/i'
		);

		foreach ( $oIterator as $oFileinfo ) {
			if( !$oFileinfo->isFile() ) {
				continue;
			}

			$sFilePath = $oFileinfo->getPathname();
			if ( strpos( $sFilePath, '/i18n/' ) === false ) {
				continue;
			}
			if ( strpos( $sFilePath, '/BlueSpice' ) === false ) {
				continue;
			}

			$oContent = FormatJson::decode( file_get_contents( $sFilePath ) );
			foreach ( $oContent as $oData ) {
				if ( $oData instanceof stdClass === false || !isset( $oData->authors ) ) {
					continue;
				}

				foreach ( $oData->authors as $sAuthor ) {
					if( !is_string( $sAuthor ) ) {
						continue;
					}
					$sAuthor = strip_tags( $sAuthor );
					$this->aTranslators[] = $sAuthor;
				}
			}
		}
	}

	protected function getGroupName() {
		return 'bluespice';
	}
}
