<?php

namespace BlueSpice\Data\Entity;

use IContextSource;

interface IStore extends \BlueSpice\Data\IStore {
	/**
	 * @return Writer
	 */
	public function getWriter( IContextSource $context = null );

	/**
	 * @return Reader
	 */
	public function getReader( IContextSource $context = null );

}
