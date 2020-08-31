<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace BlueSpice\Hook;

use BlueSpice\Hook;

abstract class ImgAuthBeforeStream extends Hook {

	/**
	 *
	 * @var Title
	 */
	protected $title = null;

	/**
	 *
	 * @var File
	 */
	protected $path = null;

	/**
	 *
	 * @var string
	 */
	protected $name = null;

	/**
	 *
	 * @var array
	 */
	protected $result = null;

	/**
	 *
	 * @param Title &$title
	 * @param string &$path
	 * @param string &$name
	 * @param array &$result
	 * @return bool
	 */
	public static function callback( &$title, &$path, &$name, &$result ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$path,
			$name,
			$result
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Title &$title
	 * @param string &$path
	 * @param string &$name
	 * @param array &$result
	 */
	public function __construct( $context, $config, &$title, &$path, &$name, &$result ) {
		parent::__construct( $context, $config );

		$this->title = &$title;
		$this->path = &$path;
		$this->name = &$name;
		$this->result = &$result;
	}
}
