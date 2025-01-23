<?php
/**
 * Hook handler base class for MediaWiki hook UploadVerifyFile
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 *
 * This file is part of BlueSpice MediaWiki
 * For further information visit https://bluespice.com
 *
 * @author     Patric Wirth
 * @package    BlueSpiceFoundation
 * @copyright  Copyright (C) 2017 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class UploadVerifyFile extends Hook {

	/**
	 *
	 * @var \UploadBase
	 */
	protected $upload = null;

	/**
	 *
	 * @var string
	 */
	protected $mime = null;

	/**
	 *
	 * @var true|array
	 */
	protected $error = null;

	/**
	 *
	 * @param \UploadBase $upload
	 * @param string $mime
	 * @param true|array &$error
	 * @return bool
	 */
	public static function callback( $upload, $mime, &$error ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$upload,
			$mime,
			$error
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param \UploadBase &$upload
	 * @param string $mime
	 * @param true|array &$error
	 */
	public function __construct( $context, $config, &$upload, $mime, &$error ) {
		parent::__construct( $context, $config );

		$this->upload = $upload;
		$this->mime = $mime;
		$this->error =& $error;
	}
}
