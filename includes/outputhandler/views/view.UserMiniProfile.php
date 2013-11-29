<?php
/**
 * This class provides a miniprofile for users.
 * @package BlueSpice_AdapterMW
 * @subpackage views
 */
class ViewUserMiniProfile extends ViewBaseElement {
	private $aDefaultClasses = array( 'bs-userminiprofile' );
	private $sUserDisplayName = '';
	private $sLinkTargetHref = '';
	private $sUserImageSrc = '';
	private $bIsInit = false;

	/**
	 * Generates the html output
	 * @param mixed $params
	 * @return string The views html
	 */
	public function execute( $params = false ) {
		if( $this->bIsInit == false ) {
			$this->init();
		}

		if( isset( $this->mOptions['print'] ) && $this->mOptions['print'] === true ) {
			return $this->sUserDisplayName.', ';
		}

		$aClasses = isset( $this->mOptions['classes'] ) && is_array( $this->mOptions['classes'] )
					? array_merge( $this->aDefaultClasses, $this->mOptions['classes'] )
					: $this->aDefaultClasses;

		$aOut = array();
		$aOut[] = '<div class="'.  implode( ' ', $aClasses ).'" title="'.$this->sUserDisplayName.'">';
		$aOut[] = empty($this->sLinkTargetHref) ? '<span class="bs-block">' :' <a class="bs-block" href="'.$this->sLinkTargetHref.'">';
		$aOut[] = '  <img alt="'.$this->sUserDisplayName.'"';
		$aOut[] = '       src="'.$this->sUserImageSrc.'"';
		$aOut[] = '       width="'.$this->mOptions['width'].'"';
		if ( BsConfig::get( 'MW::MiniProfileEnforceHeight' ) ) {
			$aOut[] = '       height="'.$this->mOptions['height'].'"';
		}
		$aOut[] = '  />';
		$aOut[] = empty($this->sLinkTargetHref) ? '</span>' :' </a>';
		$aOut[] = '</div>';
		
		$sOut = implode( "\n", $aOut );

		// CR RBV (03.06.11 08:39): Hook/Event!
		if ( BsExtensionManager::isContextActive( 'MW::SecureFileStore::Active' ) )
			$sOut = SecureFileStore::secureFilesInText($sOut);

		return $sOut;
	}
	
	/**
	 * Initializes the views members with the information from given options.
	 * @global string $wgVersion
	 * @param bool $bReInit
	 * @return void
	 */
	public function init( $bReInit = false ) {
		if( $this->bIsInit == true && $bReInit == false ) return;

		$oUser = $this->mOptions['user'];

		if( !isset( $this->mOptions['width'] ) )  $this->mOptions['width']  = 32;
		if( !isset( $this->mOptions['height'] ) ) $this->mOptions['height'] = 32;

		//Get the displayname
		$this->sUserDisplayName = 
			isset( $this->mOptions['userdisplayname'] )
				? $this->mOptions['userdisplayname']
				: BsCore::getUserDisplayName( $oUser );

		//Get the link href
		$this->sLinkTargetHref = 
			isset( $this->mOptions['linktargethref'] )
				? $this->mOptions['linktargethref']
				: htmlspecialchars( $oUser->getUserPage()->getLinkURL() );
		
		//Get the image src
		if( isset( $this->mOptions['userimagesrc'] ) ) {
			$this->sUserImageSrc = $this->mOptions['userimagesrc'];
		} else if( $oUser->isAnon() ){
			$this->sUserImageSrc = BsConfig::get( 'MW::AnonUserImage' );
			$this->sLinkTargetHref = '';
		} else {
			$sUserImageName = BsConfig::getVarForUser('MW::UserImage', $oUser);
			$this->sUserImageSrc = BsConfig::get( 'MW::DefaultUserImage' );
			if( !empty( $sUserImageName ) ) {
				$aParsedUrl = parse_url( $sUserImageName );
				if( $sUserImageName{0} == '/' ) {
					$this->sUserImageSrc = $sUserImageName;
				} else if ( isset( $aParsedUrl['scheme'] ) ) {
					$aPathInfo = pathinfo( $aParsedUrl['path'] );
					$aFileExtWhitelist = array( 'gif', 'jpg', 'jpeg', 'png' );
					$this->sUserImageSrc = $aParsedUrl['scheme'].'://'.$aParsedUrl['host'].$aParsedUrl['path'];

					if ( !in_array( mb_strtolower( $aPathInfo['extension'] ), $aFileExtWhitelist ) )
						$this->sUserImageSrc = BsConfig::get( 'MW::AnonUserImage' );
				} else {
					$oUserImageFile = RepoGroup::singleton()->findFile( Title::newFromText( $sUserImageName, NS_FILE ) );
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
						$this->sUserImageSrc = $oUserThumbnail->getUrl();
						$this->mOptions['width']  = $oUserThumbnail->getWidth();
						$this->mOptions['height'] = $oUserThumbnail->getHeight();
					}
				}
			}
		}
		wfRunHooks( 'UserMiniProfileAfterInit', array( &$this ) );
		$this->bIsInit = true;
	}
	
	public function setUserImageSrc( $sSrc ) {
		$this->sUserImageSrc = $sSrc;
	}
	
	public function getUserImageSrc() {
		return $this->sUserImageSrc;
	}
	
	public function getOptions() {
		return $this->mOptions;
	}
}