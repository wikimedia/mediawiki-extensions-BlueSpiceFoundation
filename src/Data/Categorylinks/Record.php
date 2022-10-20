<?php

namespace BlueSpice\Data\Categorylinks;

class Record extends \MWStake\MediaWiki\Component\DataStore\Record {
	public const CATEGORY_TITLE = 'category_title';
	public const CATEGORY_LINK = 'category_link';
	public const CATEGORY_IS_EXPLICIT = 'category_is_explicit';
	public const PAGE_ID = 'page_id';
}
