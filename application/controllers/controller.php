<?php
/***
 *      /$$$$$$   /$$$$$$  /$$   /$$ /$$$$$$$$ /$$$$$$$   /$$$$$$  /$$       /$$       /$$$$$$$$ /$$$$$$$
 *     /$$__  $$ /$$__  $$| $$$ | $$|__  $$__/| $$__  $$ /$$__  $$| $$      | $$      | $$_____/| $$__  $$
 *    | $$  \__/| $$  \ $$| $$$$| $$   | $$   | $$  \ $$| $$  \ $$| $$      | $$      | $$      | $$  \ $$
 *    | $$      | $$  | $$| $$ $$ $$   | $$   | $$$$$$$/| $$  | $$| $$      | $$      | $$$$$   | $$$$$$$/
 *    | $$      | $$  | $$| $$  $$$$   | $$   | $$__  $$| $$  | $$| $$      | $$      | $$__/   | $$__  $$
 *    | $$    $$| $$  | $$| $$\  $$$   | $$   | $$  \ $$| $$  | $$| $$      | $$      | $$      | $$  \ $$
 *    |  $$$$$$/|  $$$$$$/| $$ \  $$   | $$   | $$  | $$|  $$$$$$/| $$$$$$$$| $$$$$$$$| $$$$$$$$| $$  | $$
 *     \______/  \______/ |__/  \__/   |__/   |__/  |__/ \______/ |________/|________/|________/|__/  |__/
 *
 *  This is the main controller class. The framework this application runs off of is called a Model-View-Controller
 *  The models are what actually talk to the database and return information from the database
 *  The controllers are what takes information from the model and can pass it to the views
 *  The views are the visual UI that the end user sees
 */
class Controller {
	
   	public $load;
   	public $data = array();

   	//Allows other controllers to set this to see if the page is allowed to be visible to that level of access
   	protected $access;

	function __construct($view, $method = null, $parameters = null){
		 		//instantiate the load class
				$this->load = new View();
				new Model();
                
                //run any task methods
                if($method)
                	$this->runTask($method, $parameters);
				
				else{
                	$this->index();
                	$method = 'index';
                }
                //render the view
                if(file_exists('views/'.strtolower($view).'/'.strtolower($method).'.php'))
                	$this->load->load($view, $method, $this->data);
                
                else
                    $this->load->load($view, 'index', $this->data);
            	
                    



	}
	
	/*
	*The runTask() method is our way of grabbing the method from the URI string and parsing the parameters
	*/
	public function runTask($method, $parameters = null){
		
		if($method && method_exists($this, $method)) {
			 		
					//the call_user_func_array expects an array so we create a null array if parameters is empty
					if(!is_array($parameters)){
						$parameters = array();
					}
		
          call_user_func_array(array($this, $method), $parameters); 
		  
     	}
	
	}
	
	/*
	*The defaultTask() method is the one run if no task method is run. Here as a placeholder for child classes.
	*/
	public function index(){
	}
	
	
	/*
	*The set() method allows us to more easily set the view variables
	*/
	public function set($key, $value){
		
		$this->data[$key] = $value;
		
	}





}
