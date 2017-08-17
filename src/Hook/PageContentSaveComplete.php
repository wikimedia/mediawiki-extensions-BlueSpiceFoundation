<?php

namespace BlueSpice\Hook;
use BlueSpice\Hook;

abstract class PageContentSaveComplete extends Hook {

	/**
	 *
	 * @var \WikiPage
	 */
	protected $wikipage = null;

	/**
	 *
	 * @var \User
	 */
	protected $user = null;

	/**
	 *
	 * @var \Content
	 */
	protected $content = null;

	/**
	 *
	 * @var string
	 */
	protected $summary = '';

	/**
	 *
	 * @var boolean
	 */
	protected $isMinor = false;

	/**
	 *
	 * @var boolean
	 */
	protected $isWatch = false;

	/**
	 *
	 * @var int
	 */
	protected $section = 0;

	/**
	 *
	 * @var int
	 */
	protected $flags = 0;

	/**
	 *
	 * @var \Revision
	 */
	protected $revision = null;

	/**
	 *
	 * @var \Status
	 */
	protected $status = null;

	/**
	 *
	 * @var int
	 */
	protected $baseRevId = 0;

	/**
	 *
	 * @param \WikiPage $wikipage
	 * @param \User $user
	 * @param \Content $content
	 * @param string $summary
	 * @param boolean $isMinor
	 * @param boolean $isWatch
	 * @param section $section
	 * @param int $flags
	 * @param \Revision $revision
	 * @param \Status $status
	 * @param int $baseRevId
	 * @return boolean
	 */
	public static function callback( $wikipage, $user, $content, $summary, $isMinor, $isWatch, $section, $flags, $revision, $status, $baseRevId ) {
		$className = static::class;
		$hookHandler = new $className(
			null,
			null,
			$wikipage,
			$user,
			$content,
			$summary,
			$isMinor,
			$isWatch,
			$section,
			$flags,
			$revision,
			$status,
			$baseRevId
		);
		return $hookHandler->process();
	}

	/**
	 *
	 * @param \IContextSource $context
	 * @param \Context $config
	 * @param \WikiPage $wikipage
	 * @param \User $user
	 * @param \Content $content
	 * @param string $summary
	 * @param boolean $isMinor
	 * @param boolean $isWatch
	 * @param section $section
	 * @param int $flags
	 * @param \Revision $revision
	 * @param \Status $status
	 * @param int $baseRevId
	 */
	public function __construct( $context, $config, $wikipage, $user, $content, $summary, $isMinor, $isWatch, $section, $flags, $revision, $status, $baseRevId ) {
		parent::__construct( $context, $config );

		$this->wikipage = $wikipage;
		$this->user = $user;
		$this->content = $content;
		$this->summary = $summary;
		$this->isMinor = $isMinor;
		$this->section = $section;
		$this->flags = $flags;
		$this->revision = $revision;
		$this->status = $status;
		$this->baseRevId = $baseRevId;
	}
}