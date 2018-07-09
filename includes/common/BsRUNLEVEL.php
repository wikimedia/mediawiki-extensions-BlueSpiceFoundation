<?php

/**
 * BsRUNLEVEL
 *
 * Defines different runlevels of the BlueSpice framework.
 */
class BsRUNLEVEL {
	/**
	 * BsRUNLEVEL::FULL
	 * Load all extensions. This is used for normal view of the website.
	 */
	const FULL   = 1;

	/**
	 * BsRUNLEVEL::REMOTE
	 * To save server resources there are only a few extensions to be loaded.
	 * Used for API/Webservice and AJAX calls.
	 */
	const REMOTE = 2;
}
