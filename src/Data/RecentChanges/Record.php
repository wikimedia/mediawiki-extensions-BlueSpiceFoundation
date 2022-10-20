<?php

namespace BlueSpice\Data\RecentChanges;

class Record extends \MWStake\MediaWiki\Component\DataStore\Record {
	public const USER_NAME = 'user_name';
	public const USER_DISPLAY_NAME = 'user_display_name';
	public const USER_LINK = 'user_link';
	public const PAGE_PREFIXED_TEXT = 'page_prefixedtext';
	public const PAGE_NAMESPACE = 'page_namespace';
	public const PAGE_LINK = 'page_link';
	public const TIMESTAMP = 'timestamp';
	public const COMMENT_TEXT = 'comment_text';
	public const SOURCE = 'source';
	public const DIFF_URL = 'diff_url';
	public const DIFF_LINK = 'diff_link';
	public const OLDID_URL = 'oldid_url';
	public const OLDID_LINK = 'oldid_link';
	public const HIST_URL = 'hist_link';
	public const HIST_LINK = 'hist_url';
	public const RAW_TIMESTAMP = 'raw_timestamp';
	public const CUR_ID = 'cur_id';
	public const LAST_OLDID = 'last_oldid';
	public const THIS_OLDID = 'this_oldid';
	public const EXISTS = 'exists';
}
