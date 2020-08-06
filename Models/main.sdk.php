<?php
/**
 * all external libraries/vendors should be registered here
 * for full access within the application 
 * include the autoload file here
 */
$ROOT =  $_SERVER['DOCUMENT_ROOT'];
$FOLDER_NAME = basename(dirname(__FILE__));

include($ROOT . '/AuthenticationPhp' . '/Config/base.config.php');

/**
 * class autoloader
 */
function loadClasses($classname) {
    $path = __DIR__ . "/" . $classname . '.php';
    $re_path = str_replace('\\', '/', $path);
    if(file_exists($re_path) && is_readable($re_path) ) {
        include $re_path;
    }
}

spl_autoload_register('loadClasses');
?>