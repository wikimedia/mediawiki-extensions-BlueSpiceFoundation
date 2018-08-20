<?php

use BlueSpice\DynamicFileDispatcher\Params;
use BlueSpice\Services;
use BlueSpice\DynamicFileDispatcher\UserProfileImage;
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
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
			Params::MODULE => UserProfileImage::MODULE_NAME,
			UserProfileImage::USERNAME => $this->mOptions['user']->getName(),
			UserProfileImage::WIDTH => (int)$this->mOptions['width'],
			UserProfileImage::HEIGHT => (int)$this->mOptions['height']
		]);

		$dfdUrlBuilder = Services::getInstance()->getBSDynamicFileDispatcherUrlBuilder();
		$url = $dfdUrlBuilder->build( new Params( $params ) );

		$aOut = array();
		$aOut[] = '<div class="'.  implode( ' ', $aClasses ).'" title="'.$this->mOptions['userdisplayname'].'">';
		$aOut[] = empty( $this->mOptions['linktargethref'] ) ? '<span class="bs-block">' :'<a class="bs-block" href="'.$this->mOptions['linktargethref'].'">';
		$aOut[] =   '<img alt="'.$this->mOptions['userdisplayname'].'"';
		$aOut[] =        'src="'.$url.'"';
		$aOut[] =        'width="'.$this->mOptions['width'].'"';
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
}
