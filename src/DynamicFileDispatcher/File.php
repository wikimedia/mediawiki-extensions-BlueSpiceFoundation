<?php

namespace BlueSpice\DynamicFileDispatcher;
use BlueSpice\DynamicFileDispatcher\Module;

abstract class File {
	/**
	 *
	 * @var Module
	 */
	protected $dfd = null;

	/**
	 *
	 * @param Module $dfd
	 */
	public function __construct( Module $dfd ) {
		$this->dfd = $dfd;
	}

	/**
	 * Sets the headers for given \WebResponse
	 * @param \WebResponse $response
	 * @return void
	 */
	abstract public function setHeaders( \WebResponse $response );

	/**
	 * @return string
	 */
	abstract public function getMimeType();
}
