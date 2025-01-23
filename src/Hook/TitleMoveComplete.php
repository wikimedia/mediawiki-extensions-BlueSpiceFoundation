<?php

namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\Title\Title;
use MediaWiki\User\User;

/**
 * DEPRECATED!
 * @deprecated since version 3.3 - use \BlueSpice\Hook\PageMoveComplete instead
 */
abstract class TitleMoveComplete extends Hook {
	/**
	 *
	 * @var Title
	 */
	protected $title;

	/**
	 *
	 * @var Title
	 */
	protected $newTitle;

	/**
	 *
	 * @var User
	 */
	protected $user;

	/**
	 *
	 * @var int
	 */
	protected $oldid;

	/**
	 *
	 * @var int
	 */
	protected $newid;

	/**
	 *
	 * @var string
	 */
	protected $reason;

	/**
	 *
	 * @var RevisionRecord
	 */
	protected $revision;

	/**
	 *
	 * @param Title &$title
	 * @param Title &$newTitle
	 * @param User $user
	 * @param int $oldid
	 * @param int $newid
	 * @param string $reason
	 * @param RevisionRecord $revision
	 * @return bool
	 */
	public static function callback( &$title, &$newTitle, $user, $oldid, $newid, $reason,
		$revision ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$title,
			$newTitle,
			$user,
			$oldid,
			$newid,
			$reason,
			$revision
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param Title &$title
	 * @param Title &$newTitle
	 * @param User $user
	 * @param int $oldid
	 * @param int $newid
	 * @param string $reason
	 * @param RevisionRecord $revision
	 */
	public function __construct( $context, $config, &$title, &$newTitle, $user, $oldid, $newid,
		$reason, $revision ) {
		parent::__construct( $context, $config );

		$this->title =& $title;
		$this->newTitle =& $newTitle;
		$this->user = $user;
		$this->oldid = $oldid;
		$this->newid = $newid;
		$this->reason = $reason;
		$this->revision = $revision;
	}
}
