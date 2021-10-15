<?php

namespace BlueSpice;

use IContextSource;
use Message;

interface IPageInfoElement {
	public const TYPE_TEXT = 'text';
	public const TYPE_LINK = 'link';
	public const TYPE_MENU = 'menu';

	public const ITEMCLASS_PRO = 'pro';
	public const ITEMCLASS_CONTRA = 'contra';

	/**
	 *
	 * @return Message
	 */
	public function getLabelMessage();

	/**
	 *
	 * @return Message
	 */
	public function getTooltipMessage();

	/**
	 *
	 * @return string
	 */
	public function getName();

	/**
	 *
	 * @return string
	 */
	public function getUrl();

	/**
	 *
	 * @return int
	 */
	public function getPosition();

	/**
	 * @return string Can be one of IPageInfoElement::TYPE_*
	 */
	public function getType();

	/**
	 *
	 * @return string
	 */
	public function getHtmlClass();

	/**
	 *
	 * @return string
	 */
	public function getHtmlId();

	/**
	 * @return array with html attributes data-*
	 */
	public function getHtmlDataAttribs();

	/**
	 *
	 * @return string
	 */
	public function getMenu();

	/**
	 *
	 * @param IContextSource $context
	 * @return boolean
	 */
	public function shouldShow( $context );

	/**
	 * @return string Can be one of IPageInfoElement::ITEMCLASS_*
	 */
	public function getItemClass();
}
