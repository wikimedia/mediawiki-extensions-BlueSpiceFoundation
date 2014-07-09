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
			'Bartosz Dziewoski', 'Chad Horohoe', 'Raimond Spekking', 'Siebrand Mazeland',
			'Yuki Shira', 'TGC'
		);

		$sLiProgrammers = '';
		foreach ( $aProgrammers as $sProgrammer ) {
			$sLiProgrammers .= Html::element( 'li', array(), $sProgrammer );
		}

		$sLiDnT = '';
		foreach ( $aDesignAndTesting as $sDnT ) {
			$sLiDnT .= Html::element( 'li', array(), $sDnT );
		}

		$sLiContributors = '';
		foreach ( $aContributors as $sContributor ) {
			$sLiContributors .= Html::element( 'li', array(), $sContributor );
		}

		$sProgramerHeadline = Html::element( 'h3', array(), wfMessage( 'bs-credits-programmers' )->plain() );
		$sOlProgrammers = Html::openElement( 'ul' ) . $sLiProgrammers . Html::closeElement( 'ul' );

		$sDnTHeadline = Html::element( 'h3', array(), wfMessage( 'bs-credits-dnt' )->plain() );
		$sOlDnT = Html::openElement( 'ul' ) . $sLiDnT . Html::closeElement( 'ul' );

		$sContributorsHeadline = Html::element( 'h3', array(), wfMessage( 'bs-credits-contributors' )->plain() );
		$sOlContributors = Html::openElement( 'ul' ) . $sLiContributors . Html::closeElement( 'ul' );

		$sOut = Html::openElement( 'table', array( 'width' => 600 ) ) .
				Html::openElement( 'tr' ) .
					Html::openElement( 'td', array( 'style' => 'vertical-align: top;' ) ). $sProgramerHeadline. $sOlProgrammers . Html::closeElement( 'td' ).
					Html::openElement( 'td', array( 'style' => 'vertical-align: top;' ) ) . $sDnTHeadline .$sOlDnT . Html::closeElement( 'td' ).
					Html::openElement( 'td', array( 'style' => 'vertical-align: top;' ) ) . $sContributorsHeadline . $sOlContributors . Html::closeElement( 'td' ).
				Html::closeElement( 'tr' ).
				Html::closeElement( 'table' );

		$this->getOutput()->addHtml( $sOut );
	}

}