<?php

namespace BlueSpice\Data\Watchlist;

class Record extends \MWStake\MediaWiki\Component\DataStore\Record {
	public const USER_ID = 'user_id';
	public const USER_DISPLAY_NAME = 'user_display_name';
	public const USER_LINK = 'user_link';
	public const PAGE_ID = 'page_id';
	public const PAGE_PREFIXED_TEXT = 'page_prefixedtext';
	public const PAGE_LINK = 'page_link';
	public const NOTIFICATIONTIMESTAMP = 'notificationtimestamp';
	public const HAS_UNREAD_CHANGES = 'has_unread_changes';
	public const IS_TALK_PAGE = 'is_talk_page';
	public const UNREAD_CHANGES_DIFF_REVID = 'unread_changes_diff_revid';
	public const PAGE_NAMESPACE_TEXT = 'page_namespace_text';
	public const PAGE_NAMESPACE = 'page_namespace';

}
