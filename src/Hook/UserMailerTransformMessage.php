<?php
/**
 * Hook handler base class for MediaWiki hook UserMailerTransformMessage
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
 * @copyright  Copyright (C) 2020 Hallo Welt! GmbH, All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GPL-3.0-only
 * @filesource
 */
namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MailAddress;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;

abstract class UserMailerTransformMessage extends Hook {

	/**
	 *
	 * @var MailAddress[]
	 */
	protected $to = null;

	/**
	 *
	 * @var MailAddress
	 */
	protected $from = null;

	/**
	 *
	 * @var string
	 */
	protected $subject = [];

	/**
	 *
	 * @var string
	 */
	protected $headers = [];

	/**
	 *
	 * @var string|array
	 */
	protected $body = [];

	/**
	 *
	 * @var false|string
	 */
	protected $error = [];

	/**
	 *
	 * @param MailAddress[] $to
	 * @param MailAddress $from
	 * @param string &$subject
	 * @param string &$headers
	 * @param string &$body
	 * @param string &$error
	 * @return bool
	 */
	public static function callback( array $to, MailAddress $from, &$subject, &$headers,
		&$body, &$error ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$to,
			$from,
			$subject,
			$headers,
			$body,
			$error
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param MailAddress[] $to
	 * @param MailAddress $from
	 * @param string &$subject
	 * @param string &$headers
	 * @param string &$body
	 * @param string &$error
	 */
	public function __construct( $context, $config, array $to, MailAddress $from, &$subject,
		&$headers, &$body, &$error ) {
		parent::__construct( $context, $config );

		$this->to = $to;
		$this->from = $from;
		$this->subject =& $subject;
		$this->headers =& $headers;
		$this->body =& $body;
		$this->error =& $error;
	}
}
