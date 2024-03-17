<?php
/**
 * DEPRECATED! You may want to use a \BlueSpice\Renderer or a
 * \BlueSpice\TemplateRenderer instead
 */
class ViewWidget extends ViewBaseElement {

	/**
	 * Possible values 'expanded'|'collapsed'
	 * @var string
	 */
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mDefaultViewstate = '';
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mTitle = '';
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mBody = '';
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mTooltip = '';
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mAdditionalTitleClasses = [];
	// phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
	protected $_mAdditionalBodyClasses = [];

	/**
	 *
	 * @param array|false $params
	 * @return string
	 */
	public function execute( $params = false ) {
		$this->checkProperties();

		$sAdditionalTitleClasses = implode( ' ', $this->_mAdditionalTitleClasses );
		$sAdditionalBodyClasses = implode( ' ', $this->_mAdditionalBodyClasses );
		if ( is_array( $params ) && isset( $params['format'] ) && $params['format'] == 'json' ) {
			$oReturn = new stdClass();
			$oReturn->defaultViewstate = $this->_mDefaultViewstate;
			$oReturn->title = $this->_mTitle;
			$oReturn->body = $this->_mBody;
			$oReturn->tooltip = $this->_mTooltip;
			$oReturn->additionalTitleClasses = $this->_mAdditionalTitleClasses;
			$oReturn->additionalBodyClasses = $this->_mAdditionalBodyClasses;
			return $oReturn;
		}
		$aOut = [];
		$aOut[] = "<div class='bs-widget$this->_mDefaultViewstate' id='bs-widget-$this->_mId'"
			. " title='$this->_mTooltip'>";
		$aOut[] = '  <div class="bs-widget-head">';
		$aOut[] = "    <h5 class='bs-widget-title $sAdditionalTitleClasses'>$this->_mTitle</h5>";
		$aOut[] = '  </div>';
		$aOut[] = '  <div class="bs-widget-body ' . $sAdditionalBodyClasses . '">';
		$aOut[] = $this->_mBody;
		$aOut[] = '  </div>';
		$aOut[] = '</div>';

		return implode( "\n", $aOut );
	}

	private function checkProperties() {
		if ( empty( $this->_mId ) ) {
			// TODO RBV (21.10.10 09:08): Check for html id validity. See MW Sanitizer::escapeId()
			// for inspiration.
			throw new BsException( 'No id set.' );
		}
		if ( empty( $this->_mTitle ) ) {
			$this->_mTitle = $this->mId;
		}
		if ( empty( $this->_mTooltip ) ) {
			$this->_mTooltip = $this->_mTitle;
		}
		if ( empty( $this->_mBody ) ) {
			if ( $this->hasItems() ) {
				foreach ( $this->_mItems as $oViewItem ) {
					$this->_mBody .= $oViewItem->execute();
				}
			} else {
				$this->_mBody = '<ul><li><em>'
					. wfMessage( 'bs-no-information-available' )->plain()
					. '</em></li></ul>';
			}
		}
	}

	/**
	 *
	 * @param string $sTitle
	 * @return ViewWidget
	 */
	public function setTitle( $sTitle ) {
		$this->_mTitle = $sTitle;
		return $this;
	}

	/**
	 *
	 * @param string $sBody
	 * @return ViewWidget
	 */
	public function setBody( $sBody ) {
		$this->_mBody = $sBody;
		return $this;
	}

	/**
	 *
	 * @param string $sTooltip
	 * @return ViewWidget
	 */
	public function setTooltip( $sTooltip ) {
		$this->_mTooltip = $sTooltip;
		return $this;
	}

	/**
	 *
	 * @param array $aAdditionalTitleClasses
	 * @return ViewWidget
	 */
	public function setAdditionalTitleClasses( $aAdditionalTitleClasses ) {
		$this->_mAdditionalTitleClasses = $aAdditionalTitleClasses;
		return $this;
	}

	/**
	 *
	 * @param array $aAdditionalBodyClasses
	 * @return ViewWidget
	 */
	public function setAdditionalBodyClasses( $aAdditionalBodyClasses ) {
		$this->_mAdditionalBodyClasses = $aAdditionalBodyClasses;
		return $this;
	}

	/**
	 *
	 * @param string $sDefaultViewstate
	 * @return ViewWidget
	 * @throws BsException
	 */
	public function setDefaultViewstate( $sDefaultViewstate ) {
		if ( $sDefaultViewstate != 'expanded' || $sDefaultViewstate != 'collapsed' ) {
			throw new BsException(
				'"' . $sDefaultViewstate
				. '" is not a vaild viewstate. Possible values are "expanded"|"collapsed"'
			);
		}
		if ( $sDefaultViewstate == 'collapsed' ) {
			$this->_mDefaultViewstate = ' bs-widget-viewstate-collapsed';
		} else {
			$this->_mDefaultViewstate = '';
		}
		return $this;
	}

}
