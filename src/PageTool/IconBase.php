<?php

namespace BlueSpice\PageTool;

abstract class IconBase extends Base {

	/**
	 * @return string
	 */
	protected function doGetHtml() {
		$iconClass = $this->getIconClass();
		$classes = $this->getClasses();
		$tooltip = $this->getToolTip();
		$url = $this->getUrl();

		$iconHtml = \Html::element( 'i', [ 'class' => $iconClass ] );
		$anchorHtml = \Html::rawElement(
			'a',
			[
				'title' => $tooltip->text(),
				'class' => implode( ' ', $classes ),
				'href' => $url
			],
			$iconHtml
		);

		return $anchorHtml;
	}

	/**
	 * @return string
	 */
	abstract protected function getIconClass();

	/**
	 * @return string[]
	 */
	protected function getClasses() {
		return [ 'page-tool-icon' ];
	}

	/**
	 * @return \Message
	 */
	abstract protected function getToolTip();

	/**
	 * @return string
	 */
	abstract protected function getUrl();

}
