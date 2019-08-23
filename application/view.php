<?php
/***
 *     /$$    /$$ /$$$$$$ /$$$$$$$$ /$$      /$$
 *    | $$   | $$|_  $$_/| $$_____/| $$  /$ | $$
 *    | $$   | $$  | $$  | $$      | $$ /$$$| $$
 *    |  $$ / $$/  | $$  | $$$$$   | $$/$$ $$ $$
 *     \  $$ $$/   | $$  | $$__/   | $$$$_  $$$$
 *      \  $$$/    | $$  | $$      | $$$/ \  $$$
 *       \  $/    /$$$$$$| $$$$$$$$| $$/   \  $$
 *        \_/    |______/|________/|__/     \__/
 *
 *  This is the master view class, the router uses this information to find the appropriate view
 *  based off the url. The structure for views in this application is folder based so it first
 *  looks in the master /views/ directory then finds the folder that matches the name of the controller.
 *  Any PHP files in the folders are methods that MUST be declared in the respective controller
 *
 */
class View {
   function load( $folder, $file_name, $data = null )
   {
      if( is_array($data) ) {
         extract($data);
      }

      /*Example:
      If the URL is /log/new_record
      then it will go to the views/log/new_record.php to display that to the view.

      If the url is /home
      it will go to the /views/home and automatically choose the index.php file for displaying.
      */
      include 'views/' . $folder .'/' . $file_name . '.php';
   }
}



