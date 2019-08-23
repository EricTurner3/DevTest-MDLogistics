<?php
/***
 *     /$$   /$$  /$$$$$$  /$$      /$$ /$$$$$$$$
 *    | $$  | $$ /$$__  $$| $$$    /$$$| $$_____/
 *    | $$  | $$| $$  \ $$| $$$$  /$$$$| $$
 *    | $$$$$$$$| $$  | $$| $$ $$/$$ $$| $$$$$
 *    | $$__  $$| $$  | $$| $$  $$$| $$| $$__/
 *    | $$  | $$| $$  | $$| $$\  $ | $$| $$
 *    | $$  | $$|  $$$$$$/| $$ \/  | $$| $$$$$$$$
 *    |__/  |__/ \______/ |__/     |__/|________/
 *
 *  The Home controller is for grabbing the information from the database and displaying it in the views/home view
 *  It grabs a lot of information because the home view is a dashboard with many cards and charts
 */
class HomeController extends Controller{

    protected $main; //set up for using the main model to retrieve data from csv
    
    //this is the main index.php view
	public function index(){
        $this->main = new Main(); //init the main model
        $data = $this->main->getData(); //get the data
        $this->set('csv', $data); //send to the view
	}
	
	
}
