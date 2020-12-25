<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: theme_functions_include.php
| Author: Nick Jones (Digitanium)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

if(!IsSet($settings['update_version'])) {
	// pruefen, ob Einstellung bereits existiert. Falls nicht, als Version 1.0 neu anlegen
	$result = dbquery("INSERT INTO ".DB_SETTINGS." (settings_name, settings_value) VALUES ('update_version', '1.0')");
	$result1 = dbquery("UPDATE ".DB_SETTINGS." SET settings_value='7.02.07 - DE' WHERE settings_name='version'"); 
	
	/*
	WICHTIG: Sicherheitsluecke in Benutzergruppen schliessen
	Es muss sichergestellt sein, dass nach Upgrade von einer bestehenden originalen Fusion v7 dir darin evtl. bereits existierenden Gruppen
	mit ID 101-103 verschoben werden.
	*/
	// zuerst: hoechste auto-id ermitteln
	$last_id = dbarray(dbquery("SELECT group_id FROM ".DB_USER_GROUPS." ORDER BY group_id DESC LIMIT 1"));
	if($last_id['group_id']<101) { $last_id['group_id'] = 101; } // <- Vorbeugung, falls k. Usergruppe mehr in DB vorh. wäre ermittelter Wert nämlich 0, obwohl auto-increment im kritischen Bereich liegen koennte
	// auto-id mind. um 3 nach oben, um garantiert mind. 104 zu erhalten
	$result2 = dbquery("UPDATE ".DB_USER_GROUPS." SET group_id='".($last_id['group_id']+3)."' WHERE group_id='101'"); 
	$result3 = dbquery("UPDATE ".DB_USER_GROUPS." SET group_id='".($last_id['group_id']+4)."' WHERE group_id='102'"); 
	$result4 = dbquery("UPDATE ".DB_USER_GROUPS." SET group_id='".($last_id['group_id']+5)."' WHERE group_id='103'"); 
	// WICHTIG: User umsetzen, die in einer dieser Gruppen waren
	// ehem. Gruppe 101
	$result = dbquery("SELECT user_id,user_groups FROM ".DB_USERS." WHERE user_groups REGEXP('^\\\.101$|\\\.101\\\.|\\\.101$')");
	while ($data = dbarray($result)) {
		$user_groups = preg_replace(array("(^\.101$)","(\.101\.)","(\.101$)"), array(".".($last_id['group_id']+3),".".($last_id['group_id']+3).".",".".($last_id['group_id']+3)), $data['user_groups']);
		$result2 = dbquery("UPDATE ".DB_USERS." SET user_groups='$user_groups' WHERE user_id='".$data['user_id']."'");
	}
	// ehem. Gruppe 102
	$result = dbquery("SELECT user_id,user_groups FROM ".DB_USERS." WHERE user_groups REGEXP('^\\\.102$|\\\.102\\\.|\\\.102$')");
	while ($data = dbarray($result)) {
		$user_groups = preg_replace(array("(^\.102$)","(\.102\.)","(\.102$)"), array(".".($last_id['group_id']+4),".".($last_id['group_id']+4).".",".".($last_id['group_id']+4)), $data['user_groups']);
		$result2 = dbquery("UPDATE ".DB_USERS." SET user_groups='$user_groups' WHERE user_id='".$data['user_id']."'");
	}
	// ehem. Gruppe 103
	$result = dbquery("SELECT user_id,user_groups FROM ".DB_USERS." WHERE user_groups REGEXP('^\\\.103$|\\\.103\\\.|\\\.103$')");
	while ($data = dbarray($result)) {
		$user_groups = preg_replace(array("(^\.103$)","(\.103\.)","(\.103$)"), array(".".($last_id['group_id']+5),".".($last_id['group_id']+5).".",".".($last_id['group_id']+5)), $data['user_groups']);
		$result2 = dbquery("UPDATE ".DB_USERS." SET user_groups='$user_groups' WHERE user_id='".$data['user_id']."'");
	}
	/* Sicherheitsluecke geschlossen */
	
	redirect(FUSION_SELF);
	exit;
}

elseif(IsSet($settings['update_version']) && $settings['update_version'] < 1.5) {
	// v1.1 bis v1.3 beinhalten keine DB Updates, nur Datei-Updates -> Deshalb nur Aenderung der Versionsnummer durchfuehren:
	$result1 = dbquery("UPDATE ".DB_SETTINGS." SET settings_value='1.5' WHERE settings_name='update_version'");
	redirect(FUSION_SELF);
	exit;
}