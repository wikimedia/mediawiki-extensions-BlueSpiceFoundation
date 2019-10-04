<?php

namespace BlueSpice\Data\Page;

class Record extends \BlueSpice\Data\Record {
	const ID = 'page_id';
	const NS = 'page_namespace';
	const TITLE = 'page_title';
	const IS_REDIRECT = 'page_is_redirect';
	const IS_NEW = 'page_is_new';
	const ID_NEW = self::IS_NEW;
	const TOUCHED = 'page_touched';
	const LATEST = 'page_latest';
	const CONTENT_MODEL = 'page_content_model';
}
