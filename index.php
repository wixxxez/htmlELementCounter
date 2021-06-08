<?php 
//I'll used MVC model
require "connect.php";
//Controller
class Controller {
    // static function to display html page
    static public function index(){
       
     
        include "website.html";
    }
}

Controller::index();
