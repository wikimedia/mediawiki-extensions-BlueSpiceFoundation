<?php

use OOUI\NumberInputWidget;

class HTMLIntFieldOverride extends HTMLIntField {
	/**
	 * Gets the non namespaced class name
	 *
	 * @since 1.36
	 *
	 * @return string
	 */
	protected function getClassName() {
		return HTMLIntField::class;
	}

	/**
	 *
	 * @param int $value
	 * @param array $alldata
	 *
	 * @return bool
	 */
	public function validate( $value, $alldata ) {
		$p = parent::validate( $value, $alldata );

		if ( $p !== true ) {
			return $p;
		}

		if ( isset( $this->mOptions['range_min'] ) && $value < $this->mOptions['range_min'] ) {
			return $this->msg( 'htmlform-int-outofrange' )->parseAsBlock();
		}

		if ( isset( $this->mOptions['range_max'] ) && $value > $this->mOptions['range_max'] ) {
			return $this->msg( 'htmlform-int-outofrange' )->parseAsBlock();
		}

		return true;
	}

	/**
	 * Override from OOUI\Widget\HTMLTextField
	 *
	 * @param int $value
	 *
	 * @return NumberInputWidget
	 */
	public function getInputOOUI( $value ) {
		if ( !$this->isPersistent() ) {
			$value = '';
		}

		$attribs = $this->getTooltipAndAccessKeyOOUI();

		if ( $this->mClass !== '' ) {
			$attribs['classes'] = [ $this->mClass ];
		}
		if ( $this->mPlaceholder !== '' ) {
			$attribs['placeholder'] = $this->mPlaceholder;
		}

		# @todo Enforce pattern, step, required, readonly on the server side as
		# well
		$allowedParams = [
			'type',
			'min',
			'max',
			'step',
			'title',
			'maxlength',
			'tabindex',
			'disabled',
			'required',
			'autofocus',
			'readonly',
			'autocomplete',
			// Only used in OOUI mode:
			'autosize',
			'flags',
			'indicator',
		];

		/**
		 * ERM36985
		 *
		 * Alter parent method by adding pageStep and showButtons to allowedParams
		 */
		$allowedParams[] = 'pageStep';
		$allowedParams[] = 'showButtons';

		$attribs += OOUI\Element::configFromHtmlAttributes(
			$this->getAttributes( $allowedParams )
		);

		$type = $this->getType( $attribs );
		if ( isset( $attribs['step'] ) && $attribs['step'] === 'any' ) {
			$attribs['step'] = null;
		}

		return $this->getInputWidget(
			[
				'id' => $this->mID,
				'name' => $this->mName,
				'value' => $value,
				'type' => $type,
				'dir' => $this->mDir,
			] + $attribs
		);
	}
}
