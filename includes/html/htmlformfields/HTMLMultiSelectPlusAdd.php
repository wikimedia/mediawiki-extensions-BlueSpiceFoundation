<?php

/**
 * Description of HTMLMultiSelectPlusAdd
 *
 * @author Sebastian Ulbricht <x_lilu_x@gmx.de>
 */
class HTMLMultiSelectPlusAdd extends HTMLMultiSelectEx {

	function getInputHTML( $value ) {
		$html = $this->formatOptions( $this->mParams['options'], $value, 'multiselectplusadd' );
		
		$attrs = array(
			'type' => 'button',
			'id' => $this->mName . '-add',
			'title' => wfMsgExt( $this->mParams['title'], 'parseinline' ),
			'msg' => wfMsgExt( $this->mParams['message'], 'parseinline' ),
			'targetField' => $this->mName,
			'class' => 'bsMultiSelectAddButton',
			'onclick' => 'bs.util.addEntryToMultiSelect(this);',
			'value' => '+'
		);
		$button = Html::element( 'input', $attrs );
		
		$attrs = array(
			'type' => 'button',
			'id' => $this->mName . '-delete',
			'targetField' => $this->mName,
			'class' => 'bsMultiSelectDeleteButton',
			'onclick' => 'bs.util.deleteEntryFromMultiSelect(this);',
			'value' => '-'
		);
		$button .= Html::element( 'input', $attrs );
		
		return $html.'<div>'.$button.'</div>';
	}

}