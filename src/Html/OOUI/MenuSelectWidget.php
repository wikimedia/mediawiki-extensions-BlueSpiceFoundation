<?php

namespace BlueSpice\Html\OOUI;

use OOUI\InputWidget;

class MenuSelectWidget extends SelectWidget {
	use \BlueSpice\Html\OOUI\ClippableElement;
	use \BlueSpice\Html\OOUI\FloatableElement;

	protected $originalVerticalPosition;
	protected $autoHide;
	protected $hideOnChoose;
	protected $filterFromInput;
	protected $input;
	protected $widget;
	protected $autoCloseIgnore;
	protected $hightlightOnFilter;
	protected $width;
	protected $visible;

	public function __construct( array $config = [] ) {
		// Parent constructor
		parent::__construct( $config );

		$clippableConfig = array_merge( $config, [ 'clippable' => $this->group ] );
		$this->initializeClippableElement( $clippableConfig );
		$this->initializeFloatableElement( $config );

		$this->originalVerticalPosition = $this->verticalPosition;
		$this->autoHide = isset( $config['autoHide'] ) ? (bool) $config['autoHide'] : true;
		$this->hideOnChoose = isset( $config['hideOnChoose'] ) ? (bool) $config['hideOnChoose'] : true;
		$this->filterFromInput = isset( $config['filterFromInput'] ) ? (bool) $config['filterFromInput'] : false;
		$this->input = isset( $config['input'] ) ? $config['input'] : null;
		$this->widget = isset( $config['widget'] ) ? $config['widget'] : null;
		$this->autoCloseIgnore = isset( $config['autoCloseIgnore'] ) ?
			$config['autoCloseIgnore'] : new \OOUI\Tag( '' );
		$this->hightlightOnFilter = isset( $config['hightlightOnFilter'] ) ? (bool) $config['hightlightOnFilter'] : false;
		$this->width = isset( $config['width'] ) ? $config['width'] : null;

		$this->addClasses( [ "oo-ui-menuSelectWidget" ] );

		$this->visible = false;
		$this->addClasses( [ 'oo-ui-element-hidden' ] );

		$this->registerConfigCallback( function( &$config ) {
			if( $this->input ) {
				$config['input'] = $this->input;
			}
			if(  $this->widget ) {
				$config['widget'] = $this->widget;
			}
			if( $this->width ) {
				$confg['width'] = $this->width;
			}
			if( $this->autoCloseIgnore ) {
				$config['autoCloseIgnore'] = $this->autoCloseIgnore;
			}
			$config['highlightsOnFilter'] = $this->hightlightOnFilter;
			$config['autoHide'] = $this->autoHide;
			$config['hideOnChoose'] = $this->hideOnChoose;
			$config['filterFromInput'] = $this->filterFromInput;
		} );
	}

	public function getConfig( &$config ) {
		return parent::getConfig( $config );
	}

	public function getJavaScriptClassName() {
		return "OO.ui.MenuSelectWidget";
	}
}
