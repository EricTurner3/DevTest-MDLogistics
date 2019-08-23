<?php

class Main extends Model{

   
    //get the data from the csv and return to the controller
    public function getData(){
        return array_map('str_getcsv', file('./application/models/MOCK_DATA.csv'));
    }

   


}