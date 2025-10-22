<?php

namespace BlueSpice\Html\OOUI;

use OOUI\ButtonWidget;
use OOUI\Exception;
use OOUI\FieldLayout;
use OOUI\Tag;
use OOUI\TextInputWidget;

class KeyValueInputWidget extends \OOUI\Widget {
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

	/**
	 *
	 * @param array $config
	 */
	public function __construct( array $config = [] ) {
		$this->value = isset( $config['value'] )
			? $config['value']
			: [];
		$this->labelsOnlyOnFirst = isset( $config['labelsOnlyOnFirst'] )
			? (bool)$config['labelsOnlyOnFirst']
			: true;

		$this->valueRequired = isset( $config['valueRequired'] )
			? (bool)$config['valueRequired']
			: false;

		$this->keyLabel = isset( $config['keyLabel'] )
			? $config['keyLabel']
			: wfMessage( 'bs-ooui-key-value-input-widget-key-label' )->text();

		$this->valueLabel = isset( $config['valueLabel'] )
			? $config['valueLabel']
			: wfMessage( 'bs-ooui-key-value-input-widget-value-label' )->text();

		$this->addNewFormLabel = isset( $config['addNewFormLabel'] )
			? $config['addNewFormLabel']
			: wfMessage( 'bs-ooui-key-value-input-widget-add-form-label' )->text();

		$this->keyReadOnly = isset( $config['keyReadOnly'] )
			? (bool)$config['keyReadOnly']
			: false;

		$this->allowAdditions = isset( $config['allowAdditions'] )
			? (bool)$config['allowAdditions']
			: false;

		$this->separator = new Tag();
		$this->separator->addClasses( [ "bs-ooui-{$this->getWidgetId()}-separator" ] );

		parent::__construct( $config );

		$this->valueContainer = new Tag();
		$this->valueContainer->addClasses( [ "bs-ooui-{$this->getWidgetId()}-value-container" ] );

		$this->addClasses( [ "bs-ooui-widget-{$this->getWidgetId()}" ] );
		if ( !empty( $this->value ) ) {
			$this->setValue( $config['value'] );
		} else {
			$this->setNoValueMessage();
		}
		$this->appendContent( $this->valueContainer );

		if ( $this->allowAdditions ) {
			$this->addNewValueForm();
		}
	}

	protected function setNoValueMessage() {
		$label = new \OOUI\LabelWidget( [
			'label' => wfMessage( 'bs-ooui-key-value-input-widget-no-values-label' )->text()
		] );
		$label->addClasses( [ "bs-ooui-{$this->getWidgetId()}-no-value-label" ] );
		$this->valueContainer->appendContent( $label );
	}

	/**
	 *
	 * @param array $config
	 * @return null
	 */
	protected function getInputElement( $config ) {
		return null;
	}

	/**
	 * @return string
	 */
	protected function getWidgetId() {
		return 'keyValueInputWidget';
	}

	/**
	 *
	 * @param array $values
	 * @return KeyValueInputWidget
	 */
	public function setValue( $values ) {
		$first = true;
		foreach ( $values as $key => $value ) {
			$keyInput = $this->getKeyInput( $key );
			if ( $this->keyReadOnly ) {
				$keyInput->setReadOnly( true );
			}

			$valueInput = $this->getValueInput( $value );

			$showLabels = $first || !$this->labelsOnlyOnFirst;
			$keyLayout = $this->getKeyLayout( $keyInput );
			if ( !$showLabels ) {
				$keyLayout->setLabel( '' );
			}
			$valueLayout = $this->getValueLayout( $valueInput );
			if ( !$showLabels ) {
				$valueLayout->setLabel( '' );
			}

			$deleteButton = new ButtonWidget( [
					'framed' => false,
					'indicator' => 'clear'
			] );
			$deleteButton->addClasses( [ "bs-ooui-widget-{$this->getWidgetId()}-remove-btn" ] );

			$this->valueContainer->appendContent(
				$keyLayout,
				$valueLayout,
				$deleteButton,
				$this->separator
			);

			$first = false;
		}

		return $this;
	}

	/**
	 * Add form for adding a new item
	 */
	protected function addNewValueForm() {
		$this->addContainer = new Tag();
		$this->addContainer->addClasses( [
			"bs-ooui-widget-{$this->getWidgetId()}-add-container"
		] );

		if ( $this->addNewFormLabel !== '' ) {
			$this->addContainer->appendContent( new \OOUI\LabelWidget( [
				'label' => $this->addNewFormLabel
			] ) );
		}
		$this->addContainer->appendContent(
			$this->getKeyLayout( $this->getKeyInput() ),
			$this->getValueLayout( $this->getValueInput() ),
			$this->getAddButton()
		);
		$this->appendContent( $this->addContainer );
	}

	/**
	 * @param string $input
	 * @return FieldLayout
	 * @throws Exception
	 */
	protected function getValueLayout( $input ) {
		$valueLayout = new FieldLayout( $input, [ 'align' => 'top' ] );
		$valueLayout->setLabel( $this->valueLabel );

		return $valueLayout;
	}

	/**
	 * @param string $input
	 * @return FieldLayout
	 * @throws Exception
	 */
	protected function getKeyLayout( $input ) {
		$keyLayout = new FieldLayout( $input, [ 'align' => 'top' ] );
		$keyLayout->setLabel( $this->keyLabel );

		return $keyLayout;
	}

	/**
	 * @param string|null $keyValue
	 * @return TextInputWidget
	 */
	protected function getKeyInput( $keyValue = null ) {
		return new TextInputWidget( [
			'required' => true,
			'value' => $keyValue ?? ''
		] );
	}

	/**
	 * @param mixed|null $value
	 * @return TextInputWidget
	 */
	protected function getValueInput( $value = null ) {
		return new TextInputWidget( [
			'value' => $value ?? '',
			'required' => $this->valueRequired
		] );
	}

	/**
	 * @return ButtonWidget
	 */
	protected function getAddButton() {
		$addButton = new ButtonWidget( [
			'framed' => false,
			'title' => wfMessage( 'bs-ooui-key-value-input-widget-add-button-label' )->text(),
			'icon' => 'check',
			'flags' => [
				'progressive'
			]
		] );
		$addButton->addClasses( [ "bs-ooui-widget-{$this->getWidgetId()}-add-btn" ] );

		return $addButton;
	}

	/**
	 *
	 * @param array &$config
	 * @return array
	 */
	public function getConfig( &$config ) {
		if ( $this->keyLabel ) {
			$config['keyLabel'] = $this->keyLabel;
		}
		if ( $this->valueLabel ) {
			$config['valueLabel'] = $this->valueLabel;
		}
		if ( $this->valueRequired ) {
			$config['valueRequired'] = $this->valueRequired;
		}
		if ( $this->addNewFormLabel ) {
			$config['addNewFormLabel'] = $this->addNewFormLabel;
		}
		if ( $this->keyReadOnly ) {
			$config['keyReadOnly'] = $this->keyReadOnly;
		}
		if ( $this->allowAdditions ) {
			$config['allowAdditions'] = $this->allowAdditions;
		}
		if ( $this->labelsOnlyOnFirst ) {
			$config['labelsOnlyOnFirst'] = $this->labelsOnlyOnFirst;
		}
		$config['value'] = $this->value;
		return parent::getConfig( $config );
	}

	/**
	 *
	 * @return string
	 */
	public function getJavaScriptClassName() {
		return "bs.ui.widget.KeyValueInputWidget";
	}
}
