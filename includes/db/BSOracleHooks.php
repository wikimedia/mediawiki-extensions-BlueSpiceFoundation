<?php

class BSOracleHooks {
	/**
	 * PW(26.06.2012): prevents crash on oracle/postgres when reason gets to 
	 * long mysql ignores to long string by default
	 * @param Article $article
	 * @param User $user
	 * @param string $reason
	 * @param type $error
	 * @return boolean Always true to keep hook running
	 */
	public static function onArticleDelete(&$article, &$user, &$reason, &$error) {
		if (strlen($reason) > 255) {
			$reason = substr($reason, 0, 255);
		}
		return true;
	}
}