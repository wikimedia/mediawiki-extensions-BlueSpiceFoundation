<?php
/**
 * Description of HTMLMultiSelectSortList
 *
 * @author Patric Wirth <wirth@hallowelt.com>
 */
class HTMLMultiSelectSortList extends HTMLMultiSelectEx {

	function getInputHTML( $value ) {
		$this->mParent->getOutput()->addModules( 'ext.bluespice.html.formfields.sortable' );

		$aValidated = $this->reValidate($value, $this->mParams['options']);

		$aOptions = array();
		foreach( $aValidated as $aOption ) {
			$aOptions[] = $aOption['key'];
		}

		$html = $this->formatOptions( $aOptions, $value, 'multiselectsort bs-multiselect-sortable' );
		$sHTMLList = '<ul class="multiselectsortlist">';

		foreach( $aValidated as $aOption ) {
			$sHTMLList .= '<li class="multiselectsortlistitem bs-multiselect-item" data-value="'.$aOption['key'].'">'.$aOption['title'].'</li>';
		}

		$sHTMLList .= '</ul>';

		return $sHTMLList.$html;
	}


	private function reValidate( $value, $aOptions) {
		$aValidated = array();

		foreach( $value as $sValue ) {
			if( isset($aOptions[$sValue]) ) {
				$aValidated[] = array('key' => $sValue, 'title' => $aOptions[$sValue]);
				unset($aOptions[$sValue]);
			}
		}

		if(!empty($aOptions)) {
			foreach( $aOptions as $key => $sOption ) {
				$aValidated[] = array('key' => $key, 'title' => $sOption);
			}
		}

		return $aValidated;
	}

}
