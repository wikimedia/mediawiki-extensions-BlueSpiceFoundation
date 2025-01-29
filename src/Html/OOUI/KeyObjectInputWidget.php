<?php

namespace BlueSpice\Html\OOUI;

use MediaWiki\Json\FormatJson;
use OOUI\CheckboxInputWidget;
use OOUI\FieldLayout;
use OOUI\FieldsetLayout;
use OOUI\InputWidget;
use OOUI\NumberInputWidget;
use OOUI\TextInputWidget;

class KeyObjectInputWidget extends KeyValueInputWidget {
	public const TYPE_TEXT = 'text';
	public const TYPE_BOOL = 'bool';
	public const TYPE_JSON = 'json';
	public const TYPE_NUMBER = 'number';

	/** @var array */
	protected $objectConfiguration = [];

	/**
	 *
	 * @param array $config
	 */
	public function __construct( array $config = [] ) {
		$this->objectConfiguration = $config['objectConfiguration'];
		$config['valueRequired'] = true;
		parent::__construct( $config );
	}

	/**
	 * @inheritDoc
	 */
	protected function getValueInput( $value = null ) {
		$inputs = [];
		foreach ( $this->objectConfiguration as $key => $conf ) {
			$widget = $this->getWidgetForConf( $conf['type'], $conf['widget'] ?? [] );
			if ( is_array( $value ) && isset( $value[$key] ) ) {
				if ( $conf['type'] === 'json' ) {
					$value[$key] = is_string( $value[$key] )
						? $value[$key]
						: FormatJson::encode( $value );
				}
				$widget->setValue( $value[$key] );
			}
			$fieldLayout = new FieldLayout( $widget, [ 'align' => 'top' ] );
			$fieldLayout->setLabel( $conf['label'] );
			$inputs[] = $fieldLayout;
		}

		return $inputs;
	}

	/**
	 * @param string $type
	 * @param array|null $conf
	 * @return InputWidget
	 */
	protected function getWidgetForConf( $type, $conf = [] ) {
		switch ( $type ) {
			case static::TYPE_TEXT:
			case static::TYPE_JSON:
				// Json should ideally have its own widget,
				//but not critical, as parsing is happening client-side
				return new TextInputWidget( $conf );
			case static::TYPE_BOOL:
				return new CheckboxInputWidget( $conf );
			case static::TYPE_NUMBER:
				return new NumberInputWidget( $conf );
			default:
				return $this->getWidgetForConf( static::TYPE_TEXT, $conf );
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function getValueLayout( $input ) {
		$valueLayout = new FieldsetLayout();
		$valueLayout->addItems( $input );
		return $valueLayout;
	}

	/**
	 *
	 * @param array &$config
	 * @return array
	 */
	public function getConfig( &$config ) {
		$config['objectConfiguration'] = $this->objectConfiguration;
		return parent::getConfig( $config );
	}

	/**
	 *
	 * @return string
	 */
	public function getJavaScriptClassName() {
		return "bs.ui.widget.KeyObjectInputWidget";
	}
}
