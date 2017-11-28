<?php

use MediaWiki\MediaWikiServices;
use BlueSpice\DynamicFileDispatcher\Params;
/**
 * This class provides a miniprofile for users.
 * @package BlueSpice_AdapterMW
 * @subpackage views
 */
class ViewUserMiniProfile extends ViewBaseElement {

	private $aDefaultClasses = array( 'bs-userminiprofile' );
	private $bIsInit = false;

	/**
	 * Generates the html output
	 * @param mixed $params
	 * @return string The views html
	 */
	public function execute( $params = false ) {
		if ( $this->bIsInit == false ) {
			$this->init();
		}

		if ( isset( $this->mOptions['print'] ) && $this->mOptions['print'] === true ) {
			return $this->mOptions['userdisplayname'].', ';
		}

		$aClasses = isset( $this->mOptions['classes'] ) && is_array( $this->mOptions['classes'] )
			? array_merge( $this->aDefaultClasses, $this->mOptions['classes'] )
			: $this->aDefaultClasses;

		$params = array_merge( $this->mOptions, [
			Params::MODULE => 'userprofileimage',
			'username' => $this->mOptions['user']->getName(),
		]);
		$dfdUrlBuilder = MediaWikiServices::getInstance()->getService(
			'BSDynamicFileDispatcherUrlBuilder'
		);
		$url = $dfdUrlBuilder->build(
			new Params( $params )
		);

		$aOut = array();
		$aOut[] = '<div class="'.  implode( ' ', $aClasses ).'" title="'.$this->mOptions['userdisplayname'].'">';
		$aOut[] = empty( $this->mOptions['linktargethref'] ) ? '<span class="bs-block">' :'<a class="bs-block" href="'.$this->mOptions['linktargethref'].'">';
		$aOut[] =   '<img alt="'.$this->mOptions['userdisplayname'].'"';
		$aOut[] =        'src="'.$url.'"';
		$aOut[] =        'width="'.$this->mOptions['width'].'"';
		if ( BsConfig::get( 'MW::MiniProfileEnforceHeight' ) ) {
			$aOut[] =        'height="'.$this->mOptions['height'].'"';
		}
		$aOut[] =   '/>';
		$aOut[] = empty( $this->mOptions['linktargethref'] ) ? '</span>' : '</a>';
		$aOut[] = '</div>';

		$sOut = implode( "\n", $aOut );

		return $sOut;
	}

	/**
	 * TODO: Rewrite and separate all this into single methods
	 * Initializes the views members with the information from given options.
	 * @param bool $bReInit
	 * @return null
	 */
	public function init( $bReInit = false ) {
		global $wgUrlProtocols;
		if ( $this->bIsInit == true && $bReInit == false ) {
			return;
		}

		$oUser = $this->mOptions['user'];
		if( !$oUser instanceof User ) {
			throw new BsException( "No User Given. ".__CLASS__." ".__METHOD__ );
		}

		if ( !isset( $this->mOptions['width'] ) ) {
			$this->mOptions['width']
				= $GLOBALS['bsgUserMiniProfileParams']['width'];
		}
		if( !isset( $this->mOptions['height'] ) ) {
			$this->mOptions['height']
				= $GLOBALS['bsgUserMiniProfileParams']['height'];
		}

		if ( empty($this->mOptions['userdisplayname'] ) ) {
			$this->mOptions['userdisplayname'] = empty( $oUser->getRealName() )
				? $oUser->getName()
				: $oUser->getRealName()
			;
		}

		//link can be empty for an anon user
		if ( !isset( $this->mOptions['linktargethref'] ) ) {
			$this->mOptions['linktargethref'] = htmlspecialchars(
				$oUser->getUserPage()->getLinkURL(),
				ENT_QUOTES,
				'UTF-8'
			);
		}

		if( empty( $this->mOptions['userimagesrc'] ) ) {
			$this->mOptions['userimagesrc'] = $GLOBALS['wgScriptPath']
				."/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-user-default-image.png";
		}

		if ( $oUser->isAnon() ) {
			$this->mOptions['userimagesrc'] = $GLOBALS['wgScriptPath']
				."/extensions/BlueSpiceFoundation/resources/bluespice/images/bs-user-anon-image.png";
			$this->mOptions['linktargethref'] = '';
		} else {
			$sUserImageName = BsConfig::getVarForUser( 'MW::UserImage', $oUser );
			if ( !empty( $sUserImageName ) ) { //Image given as a url

				if ( $sUserImageName{0} == '/' ) {
					//relative url from own system given
					$this->mOptions['userimagesrc'] = $sUserImageName;
				} elseif ( $this->isExternalUrl( $sUserImageName ) ) {
					$aParsedUrl = wfParseUrl( $sUserImageName );
					//external url
					//TODO: Fix, when system is call via https:// and the given
					//url is http:// the browser will block the image
					$bAllowedProtocoll = in_array(
						$aParsedUrl['scheme'].$aParsedUrl['delimiter'],
						$wgUrlProtocols
					);
					if( $bAllowedProtocoll ) {
						$sQuery = isset( $aParsedUrl['query'] ) ?
							"?{$aParsedUrl['query']}"
							: ''
						;
						$this->mOptions['userimagesrc'] =
							$aParsedUrl['scheme']
							.$aParsedUrl['delimiter']
							.$aParsedUrl['host']
							.$aParsedUrl['path']
							.$sQuery
						;
					}
				} else {
					$this->setOptionsFromRepoFile( $sUserImageName );
				}
			} else {
				//MW default File:<username>
				$oUserImageFile = RepoGroup::singleton()->findFile(
					Title::newFromText( $sUserImageName, NS_FILE )
				);
				$oUserThumbnail = false;
				if ( $oUserImageFile !== false ) {
					$oUserThumbnail = $oUserImageFile->transform(
						array(
							'width' => $this->mOptions['width'],
							'height' => $this->mOptions['height']
						)
					);
				}
				if ( $oUserThumbnail !== false ) {
					$this->mOptions['userimagesrc'] = $oUserThumbnail->getUrl();
					$this->mOptions['width'] = $oUserThumbnail->getWidth();
					$this->mOptions['height'] = $oUserThumbnail->getHeight();
				}
			}
		}

		Hooks::run( 'UserMiniProfileAfterInit', array( $this ) );
		$this->bIsInit = true;
	}

	public function setUserImageSrc( $sSrc ) {
		$this->mOptions['userimagesrc'] = $sSrc;
	}

	public function getUserImageSrc() {
		return $this->mOptions['userimagesrc'];
	}

	public function getOptions() {
		return $this->mOptions;
	}

	protected function isExternalUrl( $sMaybeExternalUrl ) {
		return substr( $sMaybeExternalUrl, 0, 4 ) == "http";
	}

	protected function setOptionsFromRepoFile( $sUserImageName ) {
		$oUserImageFile = wfFindFile( $sUserImageName );
		if( $oUserImageFile ) {
			$oUserThumbnail = $oUserImageFile->transform(
				array(
					'width' => $this->mOptions['width'],
					'height' => $this->mOptions['height']
				)
			);
			if ( $oUserThumbnail ) {
				$this->mOptions['userimagesrc'] = $oUserThumbnail->getUrl();
				$this->mOptions['width'] = $oUserThumbnail->getWidth();
				$this->mOptions['height'] = $oUserThumbnail->getHeight();
			}
		}
	}
}