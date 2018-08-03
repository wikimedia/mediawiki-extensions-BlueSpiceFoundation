<?php

namespace BlueSpice\Html\OOUI;

use OOUI\InputWidget;

class MenuTagMultiselectWidget extends TagMultiselectWidget {
	protected $options = [];

	protected $overlay;
	protected $clearInputOnChoose;
	protected $menu;
	protected $menuConfig;

	public function __construct( array $config = [] ) {
		parent::__construct( $config );

		$this->overlay = $this;
		$this->clearInputOnChoose = true;
		if( isset( $config['clearInputOnChoose'] ) ) {
			$this->clearInputOnChoose = (bool) $config['clearInputOnChoose'];
		}

		$this->menuConfig = isset( $config['menu'] ) ? $config['menu'] : [];

		$menuConfig = $this->menuConfig;
		$menuConfig['widget'] = $this;
		$menuConfig['input'] = $this->hasInput ? $this->input : null;
		$menuConfig['filterFromInput'] = (bool) $this->hasInput;
		$menuConfig['autoCloseIgnore'] = $this->hasInput ? $this->input : new \OOUI\Tag( '' );
		$menuConfig['floatableContainer'] = $this->hasInput && $this->inputPosition == 'outline' ?
			$this->input : $this;
		$menuConfig['overlay'] = $this->overlay;
		$menuConfig['disabled'] = $this->isDisabled();

		$this->menu = $this->makeMenuWidget( $menuConfig );
		$this->options = $config['options'];
		$this->addOptions( $this->options );

		$this->overlay->appendContent( $this->menu );

		$this->addClasses( [ 'oo-ui-menuTagMultiselectWidget' ] );

		if( isset( $config['selected'] ) && is_array( $config['selected' ] ) ) {
			$this->selected = $config['selected'];
			$this->setValue( $this->selected );
		}

		$this->registerConfigCallback( function( &$config ) {
			if( $this->clearInputOnChoose ) {
				$config['clearInputOnChoose'] = $this->clearInputOnChoose;
			}
			if( ! empty( $this->menuConfig ) ) {
				$config['menu'] = $this->menuConfig;
			}
			if( $this->options ) {
				$config['options'] = $this->options;
			}
			if( $this->selected ) {
				$config['selected'] = $this->selected;
			}
		} );
	}

	public function getConfig( &$config ) {
		return parent::getConfig( $config );
	}

	public function getJavaScriptClassName() {
		return 'OO.ui.MenuTagMultiselectWidget';
	}

	protected function makeMenuWidget( $config ) {
		return new MenuSelectWidget( $config );
	}

	public function addOptions( $options ) {
		$items = [];
		foreach( $options as $option ) {
			$items[] = new MenuOptionWidget( [
				'data' => $option['data'],
				'label' => isset( $option['label'] ) ? $option['label'] : $option['data'],
				'icon' => isset( $option['icon'] ) ? $option['icon'] : ''
			] );
		}

		$this->menu->addItems( $items );
	}
}
