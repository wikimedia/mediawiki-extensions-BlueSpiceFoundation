<?php

/**
 * BsPATHTYPE
 *
 * When creating pathes within the BsCore framework, use PATHTYPE to specify
 * what kind of path should be created.
 */
class BsPATHTYPE {
	/**
	 * BsPATHTYPE::ABSOLUTE
	 * An absolute path is created.
	 * For example "http://www.hallowelt.biz/path/to/some/file.html".
	 */
	const ABSOLUTE = 0;

	/**
	 * BsPATHTYPE::RELATIVE
	 * A relative path is created.
	 * For example "path/to/some/file.html".
	 */
	const RELATIVE = 1;
}
