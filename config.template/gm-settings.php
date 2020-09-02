<?php
$GLOBALS['wgAdditionalGroups'] = [];

$GLOBALS['wgGroupPermissions'] = array_merge(
	$GLOBALS['wgGroupPermissions'],
	$GLOBALS['wgAdditionalGroups']
);
