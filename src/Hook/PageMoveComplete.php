<?php

namespace BlueSpice\Hook;

use BlueSpice\Hook;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Linker\LinkTarget;
use MediaWiki\Revision\RevisionRecord;
use MediaWiki\User\UserIdentity;

abstract class PageMoveComplete extends Hook {
	/**
	 *
	 * @var LinkTarget
	 */
	protected $old;

	/**
	 *
	 * @var LinkTarget
	 */
	protected $new;

	/**
	 *
	 * @var UserIdentity
	 */
	protected $userIdentity;

	/**
	 *
	 * @var int
	 */
	protected $pageid;

	/**
	 *
	 * @var int
	 */
	protected $redirid;

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
	 * @param LinkTarget $old
	 * @param LinkTarget $new
	 * @param UserIdentity $userIdentity
	 * @param int $pageid
	 * @param int $redirid
	 * @param string $reason
	 * @param RevisionRecord $revision
	 * @return bool
	 */
	public static function callback( $old, $new, $userIdentity, $pageid, $redirid, $reason,
		$revision ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$old,
			$new,
			$userIdentity,
			$pageid,
			$redirid,
			$reason,
			$revision
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param IContextSource $context
	 * @param Config $config
	 * @param LinkTarget $old
	 * @param LinkTarget $new
	 * @param UserIdentity $userIdentity
	 * @param int $pageid
	 * @param int $redirid
	 * @param string $reason
	 * @param RevisionRecord $revision
	 */
	public function __construct( $context, $config, $old, $new, $userIdentity, $pageid, $redirid,
		$reason, $revision ) {
		parent::__construct( $context, $config );

		$this->old = $old;
		$this->new = $new;
		$this->userIdentity = $userIdentity;
		$this->pageid = $pageid;
		$this->redirid = $redirid;
		$this->reason = $reason;
		$this->revision = $revision;
	}
}
