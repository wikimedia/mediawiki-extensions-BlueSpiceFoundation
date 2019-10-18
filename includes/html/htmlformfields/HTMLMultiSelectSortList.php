<?php
/**
 * Description of HTMLMultiSelectSortList
 *
 * @author Patric Wirth
 */
class HTMLMultiSelectSortList extends HTMLMultiSelectEx {

	/**
	 *
	 * @param mixed|false $value
	 * @return string
	 */
	public function getInputHTML( $value ) {
		$this->mParent->getOutput()->addModules( 'ext.bluespice.html.formfields.sortable' );

		$aValidated = $this->reValidate( $value, $this->mParams['options'] );

		$aOptions = [];
		foreach ( $aValidated as $aOption ) {
			$aOptions[] = $aOption['key'];
		}

		$html = $this->formatOptions( $aOptions, $value, 'multiselectsort bs-multiselect-sortable' );
		$sHTMLList = '<ul class="multiselectsortlist">';

		foreach ( $aValidated as $aOption ) {
			$sHTMLList .= '<li class="multiselectsortlistitem bs-multiselect-item" data-value="'
				. $aOption['key'] . '">' . $aOption['title'] . '</li>';
		}

		$sHTMLList .= '</ul>';

		return $sHTMLList . $html;
	}

	/**
	 *
	 * @param mixed|false $value
	 * @param array $aOptions
	 * @return array
	 */
	private function reValidate( $value, $aOptions ) {
		$aValidated = [];

		foreach ( $value as $sValue ) {
			if ( isset( $aOptions[$sValue] ) ) {
				$aValidated[] = [ 'key' => $sValue, 'title' => $aOptions[$sValue] ];
				unset( $aOptions[$sValue] );
			}
		}

		if ( !empty( $aOptions ) ) {
			foreach ( $aOptions as $key => $sOption ) {
				$aValidated[] = [ 'key' => $key, 'title' => $sOption ];
			}
		}

		return $aValidated;
	}

}
