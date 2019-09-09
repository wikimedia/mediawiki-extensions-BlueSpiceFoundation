<?php

namespace BlueSpice\Data\Entity;

use IContextSource;

interface IStore extends \BlueSpice\Data\IStore {
	/**
	 * @param IContextSource|null $context
	 * @return Writer
	 */
	public function getWriter( IContextSource $context = null );

	/**
	 * @param IContextSource|null $context
	 * @return Reader
	 */
	public function getReader( IContextSource $context = null );

}
