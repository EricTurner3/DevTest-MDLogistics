<?php

class GreetingController extends Controller{

   
    
    //this is the endpoint to call /greeting
	public function index(){
        //Pull from POST body, if null then check GET url parameters, else null
        $firstname = $_POST['first'] ?? $_GET['first'] ?? null;
        $lastname = $_POST['last']?? $_GET['last'] ?? null;

        $greeting = ['first' => $firstname, 'last'=>$lastname, 'greeting'=> 'Hello, ' . $firstname . ' ' . $lastname];

        $this->set('json', json_encode($greeting));
	}
	
	
}
