<?php

require_once __DIR__ . '/BSMaintenance.php';

use MediaWiki\Json\FormatJson;
use MediaWiki\MediaWikiServices;

class BSRemoteAPIBase extends BSMaintenance {

	public function __construct() {
		parent::__construct();

		$this->addOption(
			'targetapi',
			'Absolute path the target wiki\'s "api.php"',
			true,
			true
		);
		$this->addOption(
			'u',
			'A valid username on the target wiki with sufficient write permissions',
			true,
			true
		);
		$this->addOption(
			'p',
			'The users password for API login. If not provided as argument you will be promted for it',
			false,
			true
		);
	}

	protected $apiUrl = '';
	protected $username = '';
	protected $password = '';
	protected $config = '';
	protected $configArray = null;
	protected $cookieJar = null;
	protected $token = null;
	protected $edittoken = null;

	public function execute() {
		$this->apiUrl = $this->getOption( 'targetapi' );
		$this->username = $this->getOption( 'u' );
		if ( empty( $this->username ) ) {
			$this->error( '"username" can not be empty' );
			return;
		}
		$this->password = $this->getOption( 'p' );
		if ( $this->password === null ) {
			$this->password = $this->readconsole( 'Password for user "' . $this->username . '": ' );
		}
		if ( empty( $this->password ) ) {
			$this->error( '"password" can not be empty' );
			return;
		}

		if ( !$this->doAPILogin() ) {
			$this->error( 'Authentication failed' );
			return;
		}
		$this->output( 'Authentication successfull' );

		$this->getAPIEditToken();
	}

	public function doAPILogin() {
		$options = [
			'method' => 'POST',
			'postData' => [
				'action' => 'login',
				'format' => 'json',
				'lgname' => $this->username,
				'lgpassword' => $this->password
			]
		];

		// Second pass
		if ( $this->token !== null ) {
			$options['postData']['lgtoken'] = $this->token;
		}

		$req = MediaWikiServices::getInstance()->getHttpRequestFactory()
			->create( $this->apiUrl, $options );

		if ( $this->cookieJar !== null ) {
			$req->setCookieJar( $this->cookieJar );
		}

		$status = $req->execute();

		if ( $status->isOK() ) {
			$response = FormatJson::decode( $req->getContent() );

			if ( isset( $response->login ) && isset( $response->login->result ) ) {

				if ( strtolower( $response->login->result ) === 'needtoken' ) {
					$this->token = $response->login->token;
					$this->cookieJar = $req->getCookieJar();
					return $this->doAPILogin();
				} elseif ( strtolower( $response->login->result ) === 'success' ) {
					$this->cookieJar = $req->getCookieJar();
					return true;
				}
			}

			return false;
		}
		return false;
	}

	/**
	 *
	 * @param array $aOptions
	 * @return MWHttpRequest
	 */
	public function makePOSTRequest( $aOptions ) {
		$options = [
			'method' => 'POST',
			'postData' => $aOptions
		];

		$request = MediaWikiServices::getInstance()->getHttpRequestFactory()
			->create( $this->apiUrl, $options );
		$request->setCookieJar( $this->cookieJar );

		return $request;
	}

	/**
	 *
	 * @return bool
	 */
	public function getAPIEditToken() {
		$query = [
			'action' => 'query',
			'format' => 'json',
			'meta' => 'tokens',
			'type' => 'csrf',
		];

		$req = MediaWikiServices::getInstance()->getHttpRequestFactory()->create(
			wfAppendQuery( $this->apiUrl, $query )
		);
		$req->setCookieJar( $this->cookieJar );

		$status = $req->execute();

		if ( $status->isOK() ) {
			$response = FormatJson::decode( $req->getContent() );
			if ( isset( $response->query ) && isset( $response->query->tokens )
				&& isset( $response->query->tokens->csrftoken ) ) {
				$this->edittoken = $response->query->tokens->csrftoken;
				return true;
			}
		}

		return false;
	}
}

$maintClass = BSRemoteAPIBase::class;
require_once RUN_MAINTENANCE_IF_MAIN;
