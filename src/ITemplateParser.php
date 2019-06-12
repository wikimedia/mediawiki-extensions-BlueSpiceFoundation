<?php

namespace BlueSpice;

interface ITemplateParser {

	/**
	 * @param string name
	 * @param array $args
	 * @param array $scopes
	 * @return string Rendered template
	 */
	public function processTemplate( $templateName, $args, array $scopes = [] );
}
