<?php
/**
 * Special page for BlueSpice credits
 *
 * Part of BlueSpice for MediaWiki
 *
 * @author     Stephan Muggli <muggli@hallowelt.biz>
 * @package    BlueSpice_Credits
 * @subpackage Credits
 * @copyright  Copyright (C) 2013 Hallo Welt! - Medienwerkstatt GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU Public License v2 or later
 * @filesource
 */

class SpecialCredits extends BsSpecialPage {

	public function __construct() {
		parent::__construct( 'SpecialCredits' );
	}

	public function execute( $par ) {
		parent::execute( $par );
		$aProgrammers = array(
			'Markus Glaser', 'Radovan Kubani', 'Sebastian Ulbricht', 'Marc Reymann',
			'Mathias Scheer', 'Thomas Lorenz', 'Tobias Weichart', 'Robert Vogel',
			'Erwin Forster', 'Karl Waldmannstetter', 'Daniel Lynge', 'Tobias Davids',
			'Patric Wirth', 'Stephan Muggli', 'Stefan Widmann'
		);
		$aDesignAndTesting = array(
			'Anja Ebersbach', 'Richard Heigl', 'Nathalie Köpff', 'Michael Rödl',
			'Michael Scherm', 'Dardan Diugan', 'Christina Glaser', 'Christian Graf',
			'Angelika Müller', 'Jan Göttlich', 'Karl Skodnik'
		);
		$aContributors = array(
			'Aude', 'Chad Horohoe', 'Raimond Spekking', 'Siebrand Mazeland',
			'Yuki Shira', 'TGC', 'Umherirrender'
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

		$aTranslators = $this->generateTranslatorsList();

		$sLink = '<a href="https://translatewiki.net">translatewiki.net</a>';
		$aOut = array();
		$aOut[] = '<table class="wikitable">';
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

		$aOut[] = '<table class="wikitable">';
		$aOut[] = '<tr>';
		$aOut[] = '<th>'. wfMessage( 'bs-credits-translators' )->plain() .'</th>';
		$aOut[] = '<th>'. wfMessage( 'bs-credits-translation' )->plain() .'</th>';
		$aOut[] = '</tr>';
		$aOut[] = '<tr>';
		$aOut[] = '<td style="vertical-align: top;">';
		$aOut[] = '<i><h6>' . wfMessage( 'bs-credits-th', $sLink )->text() . '</h6></i>';
		$aOut[] = '<ul><li>'. implode( '</li><li>', $aTranslators['translators'] ) .'</li></ul>';
		$aOut[] = '<br />'. wfMessage( 'bs-credits-createdon', $aTranslators['ts'] )->plain();
		$aOut[] = '</td>';
		$aOut[] = '<td style="vertical-align: top;">'. $sOlTl .'</td>';
		$aOut[] = '</tr>';
		$aOut[] = '</table>';

		$this->getOutput()->addHtml(implode( "\n", $aOut ) );
	}

	public function generateTranslatorsList() {
		$vTranslators = file_get_contents( BSROOTDIR . '/includes/specials/translators.json' );
		$vTranslators = FormatJson::decode( $vTranslators );
		$aTranslators = array();
		$vTs = 0;

		foreach ( $vTranslators as $aData ) {
			if ( $aData instanceof StdClass ) {
				foreach ( $aData as $key => $sTranslator ) {
					$aTranslators['translators'][] = $sTranslator;
				}
			}
			if ( is_numeric( $aData ) ) {
				$vTs = RequestContext::getMain()->getLanguage()->date( $aData );
				$aTranslators['ts'] = $vTs;
			}
		}
		return $aTranslators;
	}
}