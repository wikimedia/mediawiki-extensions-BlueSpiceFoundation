<?php

namespace BlueSpice\Html\OOUI;

class KeyValueInputWidget extends \OOUI\Widget {
	// Properties
	protected $keyLabel;
	protected $valueLabel;
	protected $addNewFormLabel;
	protected $keyReadOnly;
	protected $allowAdditions;
	protected $labelsOnlyOnFirst;
	protected $valueRequired;
	protected $value;

	protected $separator;
	protected $valueContainer;
	protected $addContainer;

	public function __construct( array $config = [] ) {

		$this->value = isset( $config['value'] ) ? $config['value'] : [];
		$this->labelsOnlyOnFirst = isset( $config['labelsOnlyOnFirst'] ) ?
			(bool) $config['labelsOnlyOnFirst'] : true;

		$this->valueRequired = isset( $config['valueRequired'] ) ?
			(bool) $config['valueRequired'] : false;

		$this->keyLabel = isset( $config['keyLabel'] ) ?
			$config['keyLabel'] : wfMessage( 'bs-ooui-key-value-input-widget-key-label' )->plain();

		$this->valueLabel = isset( $config['valueLabel'] ) ?
			$config['valueLabel'] : wfMessage( 'bs-ooui-key-value-input-widget-value-label' )->plain();

		$this->addNewFormLabel = isset( $config['addNewFormLabel'] ) ?
			$config['addNewFormLabel'] : wfMessage( 'bs-ooui-key-value-input-widget-add-form-label' )->plain();

		$this->keyReadOnly = isset( $config['keyReadOnly'] ) ?
			(bool) $config['keyReadOnly'] : false;

		$this->allowAdditions = isset( $config['allowAdditions'] ) ?
			(bool) $config['allowAdditions'] : false;

		$this->separator = new \OOUI\Tag();
		$this->separator->addClasses( ['bs-ooui-keyValueInputWidget-separator' ] );

		parent::__construct( $config );

		$this->valueContainer = new \OOUI\Tag();
		$this->valueContainer->addClasses( [ 'bs-ooui-keyValueInputWidget-value-container' ] );

		$this->addClasses( [ 'bs-ooui-widget-keyValueInputWidget' ] );
		if( !empty( $this->value ) ) {
			$this->setValue( $config['value'] );
		} else {
			$this->setNoValueMessage();
		}
		$this->appendContent( $this->valueContainer );

		if( $this->allowAdditions ) {
			$this->addNewValueForm();
		}
	}

	protected function setNoValueMessage() {
		$label = new \OOUI\LabelWidget( [
			'label' => wfMessage( 'bs-ooui-key-value-input-widget-no-values-label' )->plain()
		] );
		$label->addClasses( [ "bs-ooui-keyValueInputWidget-no-value-label" ] );
		$this->valueContainer->appendContent( $label );
	}

	protected function getInputElement( $config ) {
		return null;
	}

	public function setValue( $values ) {
		$first = true;
		foreach( $values as $key => $value ) {
			$keyInput = new \OOUI\TextInputWidget( [
				'value' => $key
			] );
			if( $this->keyReadOnly ) {
				$keyInput->setReadOnly( true );
			}

			$valueInput = new \OOUI\TextInputWidget( [
				'value' => $value
			] );

			$showLabels = $first || !$this->labelsOnlyOnFirst;
			$layoutAttr = [];
			if( $showLabels ) {
				$layoutAttr = [
					'align' => 'top'
				];
			}
			$keyLayout = new \OOUI\FieldLayout( $keyInput, $layoutAttr );
			if( $showLabels ) {
				$keyLayout->setLabel( $this->keyLabel );
			}
			$valueLayout = new \OOUI\FieldLayout( $valueInput, $layoutAttr );
			if( $showLabels ) {
				$valueLayout->setLabel( $this->valueLabel );
			}

			$deleteButton = new \OOUI\ButtonWidget( [
					'framed' => false,
					'indicator' => 'clear'
			] );
			$deleteButton->addClasses( [ 'bs-ooui-widget-keyValueInputWidget-remove-btn' ] );

			$this->valueContainer->appendContent( $keyLayout, $valueLayout, $deleteButton, $this->separator );

			$first = false;
		}
		return $this;
	}

	protected function addNewValueForm() {
		$this->addContainer = new \OOUI\Tag();
		$this->addContainer->addClasses( [ 'bs-ooui-widget-keyValueInputWidget-add-container' ] );

		if( $this->addNewFormLabel !== '' ) {
			$this->addContainer->appendContent( new \OOUI\LabelWidget( [
				'label' => $this->addNewFormLabel
			] ) );
		}
		$keyInput = new \OOUI\TextInputWidget( [
			'required' => true
		] );

		$valueInput = new \OOUI\TextInputWidget();
		if( $this->valueRequired ) {
			$valueInput->setRequired( true );
		}

		$keyLayout = new \OOUI\FieldLayout( $keyInput, [ 'align' => 'top' ] );
		$keyLayout->setLabel( $this->keyLabel );

		$valueLayout = new \OOUI\FieldLayout( $valueInput, [ 'align' => 'top' ] );
		$valueLayout->setLabel( $this->valueLabel );

		$addButton = new \OOUI\ButtonWidget( [
			'framed' => false,
			'title' => wfMessage( 'bs-ooui-key-value-input-widget-add-button-label' )->plain(),
			'icon' => 'check',
			'flags' => [
				'progressive'
			]
		] );
		$addButton->addClasses( [ 'bs-ooui-widget-keyValueInputWidget-add-btn' ] );

		$this->addContainer->appendContent( $keyLayout, $valueLayout, $addButton );
		$this->appendContent( $this->addContainer );
	}

	public function getConfig( &$config ) {
		if( $this->keyLabel ) {
			$config['keyLabel'] = $this->keyLabel;
		}
		if( $this->valueLabel ) {
			$config['valueLabel'] = $this->valueLabel;
		}
		if( $this->valueRequired ) {
			$config['valueRequired'] = $this->valueRequired;
		}
		if( $this->addNewFormLabel ) {
			$config['addNewFormLabel'] = $this->addNewFormLabel;
		}
		if( $this->keyReadOnly ) {
			$config['keyReadOnly'] = $this->keyReadOnly;
		}
		if( $this->allowAdditions ) {
			$config['allowAdditions'] = $this->allowAdditions;
		}
		if( $this->labelsOnlyOnFirst ) {
			$config['labelsOnlyOnFirst'] = $this->labelsOnlyOnFirst;
		}
		$config['value'] = $this->value;
		return parent::getConfig( $config );
	}

	public function getJavaScriptClassName() {
		return "bs.ui.widget.KeyValueInputWidget";
	}
}
