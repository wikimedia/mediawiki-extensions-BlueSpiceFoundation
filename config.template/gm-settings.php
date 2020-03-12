<?php
global $wgAdditionalGroups;

$wgAdditionalGroups = [];

$wgGroupPermissions = array_merge( $wgGroupPermissions, $wgAdditionalGroups );
