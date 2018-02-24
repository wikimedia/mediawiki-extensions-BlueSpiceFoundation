<?php

namespace BlueSpice;

/**
 * See https://www.mediawiki.org/wiki/Manual:Logging_to_Special:Log
 * and https://github.com/wikimedia/mediawiki-extensions-BlueSpiceFoundation/blob/05e031ffb070251a0a52ef52bcef92a81adb1593/includes/api/BSApiTasksBase.php#L212
 * Inspired by https://github.com/SemanticMediaWiki/SemanticMediaWiki/blob/52b36c0e33cc61592954c9159372b52d13d5a411/src/MediaWiki/ManualEntryLogger.php
 */
class ActionLogger {

	const OPT_PERFORMER = 'performer';
	const OPT_TARGET = 'target';
	const OPT_TIMESTAMP = 'timestamp';
	const OPT_RELATIONS = 'relations';
	const OPT_COMMENT = 'comment';
	const OPT_DELETED = 'deleted';

	/**
	 *
	 * @var string
	 */
	protected $type = '';

	/**
	 *
	 * @var \User
	 */
	protected $performer = null;

	/**
	 *
	 * @var \Title
	 */
	protected $target = null;


	/**
	 *
	 * @param string $type
	 * @param \User $performer
	 * @param \Title $target
	 */
	public function __construct( $type, $performer, $target ) {
		$this->type = $type;
		$this->performer = $performer;
		$this->target = $target;
	}

	/**
	 *
	 * @param string $action
	 * @param array $params
	 * @param array $options
	 * @param boolean $publish Whether to list in recent changes or not
	 * @return int
	 */
	public function log( $action, $params, $options = array(), $publish = false ) {
		$options += [
			self::OPT_PERFORMER => null,
			self::OPT_TARGET => null,
			self::OPT_TIMESTAMP => null,
			self::OPT_RELATIONS => null,
			self::OPT_COMMENT => null
		];

		$target = $options[self::OPT_TARGET];
		if ( $target === null ) {
			$target = $this->target;
		}

		$performer = $options[self::OPT_PERFORMER];
		if ( $performer === null ) {
			$performer = $this->performer;
		}

		$logEntry = $this->newLogEntry( $action );
		$logEntry->setPerformer( $performer );
		$logEntry->setTarget( $target );
		$logEntry->setParameters( $params );

		if ( $options[self::OPT_TIMESTAMP] !== null ) {
			$logEntry->setTimestamp( $options[self::OPT_TIMESTAMP] );
		}

		if ( $options[self::OPT_RELATIONS] !== null ) {
			$logEntry->setRelations( $options[self::OPT_RELATIONS] );
		}

		if ( $options[self::OPT_COMMENT] !== null ) {
			$logEntry->setComment( $options[self::OPT_COMMENT] );
		}

		if ( $options[self::OPT_DELETED] !== null ) {
			$logEntry->setDeleted( $options[self::OPT_DELETED] );
		}

		$entryId = $logEntry->insert();

		if ( $publish ) {
			$logEntry->publish();
		}

		return $entryId;
	}

	protected function newLogEntry( $subtype ) {
		return new \ManualLogEntry( $this->type, $subtype );
	}
}
