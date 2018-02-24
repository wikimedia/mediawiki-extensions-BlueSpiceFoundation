<?php
global $wgAdditionalGroups;

$wgAdditionalGroups = array();


$wgGroupPermissions = array_merge($wgGroupPermissions, $wgAdditionalGroups);
