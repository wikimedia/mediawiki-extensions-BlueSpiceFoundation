<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BlueSpice;

interface ITemplate {
	/**
	 *
	 * @param array $args
	 * @param array $scopes
	 * @return string rendered template
	 */
	public function process( array $args = [], array $scopes = [] );

	/**
	 * @return string
	 */
	public function getFilePath();
}
