<?php

namespace BlueSpice\Html\Descriptor;

use MediaWiki\Message\Message;

interface ILink {

	/**
	 * @return string
	 */
	public function getHtmlId();

	/**
	 * @return Message
	 */
	public function getLabel();

	/**
	 * @return string
	 */
	public function getHref();

	/**
	 * @return Message
	 */
	public function getTooltip();

	/**
	 * @return string[]
	 */
	public function getCSSClasses();

	/**
	 * @return array
	 */
	public function getDataAttributes();

	/**
	 * @return string
	 */
	public function getIcon();

	/**
	 * TODO: How about "access key"?
	 */
}
