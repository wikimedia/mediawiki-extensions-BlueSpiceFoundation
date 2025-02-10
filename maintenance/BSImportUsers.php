<?php

use MediaWiki\CommentStore\CommentStoreComment;
use MediaWiki\Content\ContentHandler;
use MediaWiki\MediaWikiServices;
use MediaWiki\Revision\SlotRecord;
use MediaWiki\User\User;

require_once __DIR__ . '/BSMaintenance.php';

class BSImportUsers extends BSMaintenance {
	public function __construct() {
		parent::__construct();

		$this->addOption( 'src', 'The path to the source file', true, true );
		$this->addOption(
			'defaultpw',
			'A password that should be set for any new user',
			false,
			true
		);
		$this->addOption(
			'createuserpage',
			'Wether or not a user page should be created (<userpage> element needs to be available)',
			false,
			false
		);
	}

	public function execute() {
		$oDOM = new DOMDocument();
		$oDOM->load( $this->getOption( 'src' ) );
		$oDOM->recover = true;

		$oUserNodes = $oDOM->getElementsByTagName( 'user' );
		$services = MediaWikiServices::getInstance();
		$userFactory = $services->getUserFactory();
		$userOptionsManager = $services->getUserOptionsManager();
		$wikiPageFactory = $services->getWikiPageFactory();
		$userGroupManager = $services->getUserGroupManager();
		foreach ( $oUserNodes as $oUserNode ) {
			$sUserName = $this->getChildNodeValue( $oUserNode, 'name' );
			$oUser = $userFactory->newFromName( $sUserName );
			if ( $oUser instanceof User === false ) {
				$this->output( $sUserName . ' is not a valid username' );
				continue;
			}

			if ( $oUser->getId() !== 0 ) {
				$this->output( $oUser->getName() . 'already exists. UserID: ' . $oUser->getId() );
				// TODO: make optional
				$this->output( 'Skipping!' );
				continue;
			}

			$sUserRealName = $this->getChildNodeValue( $oUserNode, 'realname' );
			if ( !empty( $sUserRealName ) ) {
				$oUser->setRealName( $sUserRealName );
			}

			$sUserEmail = $this->getChildNodeValue( $oUserNode, 'email' );
			if ( !empty( $sUserEmail ) ) {
				$oUser->setEmail( $sUserEmail );
			}

			// TODO: maybe write 'touched', 'registration', etc. directly to DB?

			$oProperties = $oUserNode->getElementsByTagName( 'property' );
			foreach ( $oProperties as $oProperty ) {
				$userOptionsManager->setOption(
					$oUser,
					$oProperty->getAttribute( 'name' ),
					$oProperty->getAttribute( 'value' )
				);
			}

			$oStatus = $oUser->addToDatabase();
			if ( $oStatus->isOK() ) {
				$this->output(
					$oUser->getName() . ' successfully added to database. UserID: ' . $oUser->getId()
				);
			} else {
				$this->error(
					$oUser->getName() . ' could not be added to database. Message '
					. $oStatus->getMessage()->plain()
				);
				continue;
			}

			$sUserPassword = $this->getOption( 'defaultpw', '' );
			if ( !empty( $sUserPassword ) ) {
				$oUser->changeAuthenticationData( [ 'password' => $sUserPassword ] );
				$oUser->saveSettings();
			}

			$oGroups = $oUserNode->getElementsByTagName( 'group' );
			foreach ( $oGroups as $oGroup ) {
				$userGroupManager->addUserToGroup( $oUser, $oGroup->getAttribute( 'name' ) );
			}

			if ( $this->getOption( 'createuserpage', false ) ) {
				if ( $oUser->getUserPage()->exists() ) {
					continue;
				}
				$sContent = $this->getChildNodeValue( $oUserNode, 'userpage' );
				if ( empty( $sContent ) ) {
					continue;
				}

				$oContent = ContentHandler::makeContent(
					$sContent,
					$oUser->getUserPage()
				);

				$oWikiPage = $wikiPageFactory->newFromTitle( $oUser->getUserPage() );
				$updater = $oWikiPage->newPageUpdater( $oUser );
				$updater->setContent( SlotRecord::MAIN, $oContent );
				$comment = CommentStoreComment::newUnsavedComment( __CLASS__ );
				try {
					$updater->saveRevision( $comment );
				} catch ( Exception $e ) {
					$this->error( $e->getMessage() );
				}
				$oEditStatus = $updater->getStatus();

				if ( $oEditStatus->isOK() ) {
					$this->output(
						'Page ' . $oUser->getUserPage()->getPrefixedText() . ' successfully created.'
					);
				} else {
					$this->error(
						'Page ' . $oUser->getUserPage()->getPrefixedText()
						. ' could not be created. Message: '
						. $oEditStatus->getMessage()->plain()
					);
				}
			}
		}
	}

	/**
	 *
	 * @param DOMElement $oNode
	 * @param string $sChildNodeName
	 * @return string
	 */
	public function getChildNodeValue( $oNode, $sChildNodeName ) {
		$oChildNode = $oNode->getElementsByTagName( $sChildNodeName )->item( 0 );
		if ( $oChildNode instanceof DOMElement === false ) {
			return '';
		}

		return $oChildNode->nodeValue;
	}

}

$maintClass = BSImportUsers::class;
require_once RUN_MAINTENANCE_IF_MAIN;
