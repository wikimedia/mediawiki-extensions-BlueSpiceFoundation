<?php

namespace BlueSpice\Data\Page;

class Record extends \MWStake\MediaWiki\Component\DataStore\Record {
	public const ID = 'page_id';
	public const NS = 'page_namespace';
	public const TITLE = 'page_title';
	public const IS_REDIRECT = 'page_is_redirect';
	public const IS_NEW = 'page_is_new';
	public const ID_NEW = self::IS_NEW;
	public const TOUCHED = 'page_touched';
	public const LATEST = 'page_latest';
	public const CONTENT_MODEL = 'page_content_model';
}
