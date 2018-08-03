<?php

namespace BlueSpice\Html\OOUI;

trait ClippableElement {
	protected $clippable;

	protected $clippableContainer;

	public function initializeClippableElement( array $config = [] ) {
		if( isset( $config['clippableContainer'] ) ) {
			$this->setClippableContainer( $config['clippableContainer'] );
		}

		$clippable = $config['clippable'] ? $config['clippable'] : new \OOUI\Tag();
		$this->setClippableElement( $clippable );
	}

	protected function setClippableContainer( $clippableContainer ) {
		$this->clippableContainer = $clippableContainer;
	}

	protected function setClippableElement( $clippable ) {
		if( $this->clippable && $this->clippable instanceof \OOUI\Tag ) {
			$this->clippable->removeClasses( [ "oo-ui-clippableElement-clippable" ] );
			$this->clippable->setAttributes( [
				'style' => "width: ''; height: ''; overflowX: ''; overflowY: ''"
			] );
		}

		$clippable instanceof \OOUI\Tag;
		$clippable->addClasses( [ 'oo-ui-clippableElement-clippable' ] );
		$this->clippable = $clippable;
	}
}
