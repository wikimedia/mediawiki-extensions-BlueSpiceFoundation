<?php
class ViewUserBarElement extends ViewBaseElement {

	protected $sIcon = '';
	protected $sTooltip = '';
	protected $sText = '';
	protected $sLink = '';
	protected $sId = '';

	public function execute( $params = false ) {
		$sOut = '';
		$sOut .= $this->renderElement();
		return $sOut;

	}

	public function setIcon( $sIcon ) {
		$this->sIcon = $sIcon;
	}

	public function setTooltip( $sTooltip ) {
		$this->sTooltip = $sTooltip;
	}

	public function setText( $sText ) {
		$this->sText = $sText;
	}

	public function setLink( $sLink ) {
		$this->sLink = $sLink;
	}

	public function setId( $sId ) {
		$this->sId = $sId;
	}

	protected function renderElement() {
		$aOut = array();
		$aOut[] = '<div id="'.$this->sId.'">';
		$aOut[] = '  <a title="'.$this->sTooltip.'" href="'.$this->sLink.'">';
		$aOut[] = '    <img alt="'.$this->sTooltip.'" src="'.$this->sIcon.'">';
		$aOut[] = $this->sText;
		$aOut[] = '  </a>';
		$aOut[] = '</div>';
		return join("\n", $aOut);
	}
}