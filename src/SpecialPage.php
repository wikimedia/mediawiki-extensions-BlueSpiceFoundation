<?php

namespace BlueSpice;

use MediaWiki\Auth\AuthManager;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Context\RequestContext;
use MediaWiki\MediaWikiServices;
use MediaWiki\SpecialPage\SpecialPage as MediaWikiSpecialPage;

abstract class SpecialPage extends MediaWikiSpecialPage {

	/** @var MediaWikiServices */
	protected $services = null;

	/**
	 * @param string $name Name of the special page, as seen in links and URLs
	 * @param string $restriction User right required, e.g. "block" or "delete"
	 * @param bool $listed Whether the page is listed in Special:Specialpages
	 * @param callable|bool $function Unused
	 * @param string $file Unused
	 * @param bool $includable Whether the page can be included in normal pages
	 */
	public function __construct(
		$name = '', $restriction = '', $listed = true,
		$function = false, $file = '', $includable = false
	) {
		parent::__construct(
			$name, $restriction, $listed,
			$function, $file, $includable
		);
		$this->services = MediaWikiServices::getInstance();
	}

	/**
	 * Actually render the page content.
	 * @param string $sParameter URL parameters to special page.
	 */
	public function execute( $sParameter ) {
		$this->setHeaders();
		$this->checkPermissions();
		$securityLevel = $this->getLoginSecurityLevel();
		if ( $securityLevel !== false && !$this->checkLoginSecurityLevel( $securityLevel ) ) {
			return;
		}
		$this->outputHeader();
	}

	/**
	 *
	 * @return string
	 */
	protected function getGroupName() {
		return 'bluespice';
	}

	/**
	 * Shortcut to get main config object
	 * @return Config
	 * @since 1.24
	 */
	public function getConfig() {
		return $this->services->getConfigFactory()->makeConfig( 'bsg' );
	}

	/**
	 * Gets the context this SpecialPage is executed in
	 *
	 * @return IContextSource|RequestContext
	 * @since 1.18
	 */
	public function getContext() {
		return new Context( parent::getContext(), $this->getConfig() );
	}

	/**
	 * This needs to be overwritten in order to NOT force the re-login with ssl
	 * as there are a lot of installations not running with ssl at all
	 *
	 * Verifies that the user meets the security level, possibly reauthenticating them in the process.
	 *
	 * This should be used when the page does something security-sensitive and needs extra defense
	 * against a stolen account (e.g. a reauthentication). The authentication framework will make
	 * an extra effort to make sure the user account is not compromised. What that exactly means
	 * will depend on the system and user settings; e.g. the user might be required to log in again
	 * unless their last login happened recently, or they might be given a second-factor challenge.
	 *
	 * Calling this method will result in one if these actions:
	 * - return true: all good.
	 * - return false and set a redirect: caller should abort; the redirect will take the user
	 *   to the login page for reauthentication, and back.
	 * - throw an exception if there is no way for the user to meet the requirements without using
	 *   a different access method (e.g. this functionality is only available from a specific IP).
	 *
	 * Note that this does not in any way check that the user is authorized to use this special page
	 * (use checkPermissions() for that).
	 *
	 * @param string|null $level A security level. Can be an arbitrary string, defaults to the page
	 *   name.
	 * @return bool False means a redirect to the reauthentication page has been set and processing
	 *   of the special page should be aborted.
	 * @throws ErrorPageError If the security level cannot be met, even with reauthentication.
	 */
	protected function checkLoginSecurityLevel( $level = null ) {
		$level = $level ?: $this->getName();
		$key = 'SpecialPage:reauth:' . $this->getName();
		$request = $this->getRequest();

		$securityStatus = $this->services->getAuthManager()
			->securitySensitiveOperationStatus( $level );
		if ( $securityStatus === AuthManager::SEC_OK ) {
			$uniqueId = $request->getVal( 'postUniqueId' );
			if ( $uniqueId ) {
				$key = $key . ':' . $uniqueId;
				$session = $request->getSession();
				$data = $session->getSecret( $key );
				if ( $data ) {
					$session->remove( $key );
					$this->setReauthPostData( $data );
				}
			}
			return true;
		} elseif ( $securityStatus === AuthManager::SEC_REAUTH ) {
			$title = self::getTitleFor( 'Userlogin' );
			$queryParams = $request->getQueryValues();

			if ( $request->wasPosted() ) {
				$data = array_diff_assoc( $request->getValues(), $request->getQueryValues() );
				if ( $data ) {
					// unique ID in case the same special page is open in multiple browser tabs
					$uniqueId = \MWCryptRand::generateHex( 6 );
					$key = $key . ':' . $uniqueId;
					$queryParams['postUniqueId'] = $uniqueId;
					$session = $request->getSession();
					// Just in case
					$session->persist();
					$session->setSecret( $key, $data );
				}
			}

			$query = [
				'returnto' => $this->getFullTitle()->getPrefixedDBkey(),
				'returntoquery' => wfArrayToCgi( array_diff_key( $queryParams, [ 'title' => true ] ) ),
				'force' => $level,
			];
			// PW(20180815): ssl should not be forced
			$url = $title->getFullURL( $query, false, PROTO_CURRENT );
			// $url = $title->getFullURL( $query, false, PROTO_HTTPS );

			$this->getOutput()->redirect( $url );
			return false;
		}

		$titleMessage = $this->msg( 'specialpage-securitylevel-not-allowed-title' );
		$errorMessage = $this->msg( 'specialpage-securitylevel-not-allowed' );
		throw new \ErrorPageError( $titleMessage, $errorMessage );
	}
}
