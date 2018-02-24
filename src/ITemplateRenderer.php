<?php

namespace BlueSpice;

interface ITemplateRenderer {
	/**
	 * @return string
	 */
	public function getTemplateName();
	/**
	 * @return array
	 */
	public function getArgs();
}
