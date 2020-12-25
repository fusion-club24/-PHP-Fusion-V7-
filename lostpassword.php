<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2011 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: lostpassword.php
| Author: Nick Jones (Digitanium)
+--------------------------------------------------------+
| php 8 version angepasst 
| Author: Matthias Schubert (21Matze)
+-------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
require_once "maincore.php";
require_once THEMES."templates/header.php";
require_once INCLUDES."sendmail_include.php";
include LOCALE.LOCALESET."lostpassword.php";
#require CLASSES."PasswordAuth.class.php";
# require CLASSES."LostPassword.class.php";
if (iMEMBER) redirect("index.php");
/** alter php 7.4 code
/*function  __autoload($class) {
  require CLASSES.$class.".class.php";
    #include 'classes/' . $class . '.class.php';
 if (!class_exists($class)) { die("Class not found"); }
} */     
 
     /**      neuer php 8 code         **/
     if(!function_exists('classAutoLoader')){
        function classAutoLoader($class){
            $class=strtolower($class);
            $classFile= require CLASSES.$class.".class.php";
            if(is_file($classFile)&&!class_exists($class)) include $classFile;
        }
    }
    spl_autoload_register('classAutoLoader');
    

add_to_title($locale['global_200'].$locale['400']);
opentable($locale['400']);

$obj = new LostPassword();
if (isset($_GET['user_email']) && isset($_GET['account'])) {
	$obj->checkPasswordRequest($_GET['user_email'], $_GET['account']);
	$obj->displayOutput();
} elseif (isset($_POST['send_password'])) {
	$obj->sendPasswordRequest($_POST['email']);
	$obj->displayOutput();
} else {
	$obj->renderInputForm();
	$obj->displayOutput();
}

closetable();

require_once THEMES."templates/footer.php";
?>