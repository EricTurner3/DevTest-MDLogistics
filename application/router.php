<?php
/***
 *     /$$$$$$$   /$$$$$$  /$$   /$$ /$$$$$$$$ /$$$$$$$$ /$$$$$$$
 *    | $$__  $$ /$$__  $$| $$  | $$|__  $$__/| $$_____/| $$__  $$
 *    | $$  \ $$| $$  \ $$| $$  | $$   | $$   | $$      | $$  \ $$
 *    | $$$$$$$/| $$  | $$| $$  | $$   | $$   | $$$$$   | $$$$$$$/
 *    | $$__  $$| $$  | $$| $$  | $$   | $$   | $$__/   | $$__  $$
 *    | $$  \ $$| $$  | $$| $$  | $$   | $$   | $$      | $$  \ $$
 *    | $$  | $$|  $$$$$$/|  $$$$$$/   | $$   | $$$$$$$$| $$  | $$
 *    |__/  |__/ \______/  \______/    |__/   |________/|__/  |__/
 *
 *  The router is what actually parses the URL and routes information between the views and controllers.
 *
 */
session_start();

/*************************************************************************************************
 *    ______               _                                  _    ___  ___          _
 *    |  _  \             | |                                | |   |  \/  |         | |
 *    | | | |_____   _____| | ___  _ __  _ __ ___   ___ _ __ | |_  | .  . | ___   __| | ___
 *    | | | / _ \ \ / / _ \ |/ _ \| '_ \| '_ ` _ \ / _ \ '_ \| __| | |\/| |/ _ \ / _` |/ _ \
 *    | |/ /  __/\ V /  __/ | (_) | |_) | | | | | |  __/ | | | |_  | |  | | (_) | (_| |  __/
 *    |___/ \___| \_/ \___|_|\___/| .__/|_| |_| |_|\___|_| |_|\__| \_|  |_/\___/ \__,_|\___|
 *                                | |
 *                                |_|
 *
 * Here we can set the Development Mode and Version of the Application
 *
 * Development Mode Flags:
 * 0 - Production
 * 1 - Development Mode (no logging)
 * 2 - Development Mode (verbose error logging)
 *
 * It is important to note setting the application into production or development mode will change the
 * main database being used. If in production it uses a production database for damage records, otherwise
 * it uses a testing database just to see what the data looks like
 *
 * The version can be changed here and is displayed on the footer of every page. Can be useful when a release
 * is set on GitHub (if we choose to release things as a version)
 */

$_SESSION['version'] = '1.0';
$_SESSION['development_mode']=0;
// Development Mode 2 displays the verbose PHP error logs to the front-end
if($_SESSION['development_mode']==2){
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

/**************************************************************************************************/

//Resume router class
function autoloader($class){
	
		if(file_exists('application/'.strtolower($class).'.php')){
			//first check the application directory
			include_once('application/'.strtolower($class).'.php');
			
		}elseif(file_exists('application/controllers/'.strtolower($class).'.php')){
			//then check the controller directory
			include_once('application/controllers/'.strtolower($class).'.php');

		}elseif(file_exists('application/models/'.strtolower($class).'.php')){
			//finally check the models directory
			include_once('application/models/'.strtolower($class).'.php');
			
		}
	
	
}
require_once('application/config.php');
spl_autoload_register('autoloader');

//grab the path info and break it apart into separate variables
$paths= explode('/', $_SERVER['PATH_INFO']);
//check the view, if empty set to default view
    if ($paths[1] == '' ) {
        $view = DEFAULT_VIEW;
    } else {
        $view = $paths[1];
    }
//check to see if a method is being called and assign the $method variable
    $method = $paths[2];

//check to see if any parameters are passed and assign the $parameters array
    if (isset($paths[3])) {
        for ($i = 3; $i < count($paths); $i++) {

            $parameters[] = $paths[$i];
        }
    } else {
        $parameters = null;
    }



//uppercase the first variable name and append Controller to it. If none, the default controller will load
$controller = $view.'Controller';
//instantiate our controller and pass in parameters
if (class_exists($controller)) {
    new $controller($view, $method, $parameters);
} else {
		new Controller('404');
}

