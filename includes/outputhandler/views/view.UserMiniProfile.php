<?php
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

		$aOut = array();
		$aOut[] = '<div class="'.  implode( ' ', $aClasses ).'" title="'.$this->mOptions['userdisplayname'].'">';
		$aOut[] = empty( $this->mOptions['linktargethref'] ) ? '<span class="bs-block">' :'<a class="bs-block" href="'.$this->mOptions['linktargethref'].'">';
		$aOut[] =   '<img alt="'.$this->mOptions['userdisplayname'].'"';
		$aOut[] =        'src="'.$this->mOptions['userimagesrc'].'"';
		$aOut[] =        'width="'.$this->mOptions['width'].'"';
		if ( BsConfig::get( 'MW::MiniProfileEnforceHeight' ) ) {
			$aOut[] =        'height="'.$this->mOptions['height'].'"';
		}
		$aOut[] =   '/>';
		$aOut[] = empty( $this->mOptions['linktargethref'] ) ? '</span>' : '</a>';
		$aOut[] = '</div>';

		$sOut = implode( "\n", $aOut );

		// CR RBV (03.06.11 08:39): Hook/Event!
		if ( BsExtensionManager::isContextActive( 'MW::SecureFileStore::Active' ) )
			$sOut = SecureFileStore::secureFilesInText($sOut);

		return $sOut;
	}

	/**
	 * Initializes the views members with the information from given options.
	 * @param bool $bReInit
	 * @return null
	 */
	public function init( $bReInit = false ) {
		global $wgUrlProtocols;
		if ( $this->bIsInit == true && $bReInit == false ) return;

		$oUser = $this->mOptions['user'];

		if ( !isset( $this->mOptions['width'] ) ) $this->mOptions['width']  = 32;
		if( !isset( $this->mOptions['height'] ) ) $this->mOptions['height'] = 32;

		if ( !isset( $this->mOptions['userdisplayname'] ) ) {
			$this->mOptions['userdisplayname'] = BsCore::getUserDisplayName( $oUser );
		}

		if ( !isset( $this->mOptions['linktargethref'] ) ) {
			$this->mOptions['linktargethref'] = htmlspecialchars( $oUser->getUserPage()->getLinkURL(), ENT_QUOTES, 'UTF-8' );
		}

		if ( isset ( $this->mOptions['userimagesrc'] ) ) {
			$this->mOptions['userimagesrc'] = $this->mOptions['userimagesrc'];
		} elseif ( $oUser->isAnon() ){
			$this->mOptions['userimagesrc'] = BsConfig::get( 'MW::AnonUserImage' );
			$this->mOptions['linktargethref'] = '';
		} else {
			$sUserImageName = BsConfig::getVarForUser('MW::UserImage', $oUser);
			$this->mOptions['userimagesrc'] = BsConfig::get( 'MW::DefaultUserImage' );
			if ( !empty( $sUserImageName ) ) {
				$aParsedUrl = parse_url( $sUserImageName );
				if ( $sUserImageName{0} == '/' ) {
					$this->mOptions['userimagesrc'] = $sUserImageName;
				} elseif ( isset( $aParsedUrl['scheme'] ) && in_array( $aParsedUrl['scheme'], $wgUrlProtocols ) ) {
					$aPathInfo = pathinfo( $aParsedUrl['path'] );
					$aFileExtWhitelist = BsConfig::get( 'MW::ImageExtensions' );
					$this->mOptions['userimagesrc'] = $aParsedUrl['scheme'].'://'.$aParsedUrl['host'].$aParsedUrl['path'];

					if ( isset( $aPathInfo['extension'] ) && !in_array( strtolower( $aPathInfo['extension'] ), $aFileExtWhitelist ) ){
						$this->mOptions['userimagesrc'] = BsConfig::get( 'MW::AnonUserImage' );
					}
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
						$this->mOptions['userimagesrc'] = $oUserThumbnail->getUrl();
						$this->mOptions['width'] = $oUserThumbnail->getWidth();
						$this->mOptions['height'] = $oUserThumbnail->getHeight();
					}
				}
			}
		}

		wfRunHooks( 'UserMiniProfileAfterInit', array( &$this ) );
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
}