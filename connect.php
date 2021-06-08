<?php
//pass:: 9H0fa2Lbb90iKmaY
session_start(); 
require "rb.php";

/* we create a trait for 2 reason
    1. We can use this trait in other classes when we want to change our request
    2. I dont like fat classes
*/ 

trait DataBaseExecution {
   
    public function addRequest(string $host,string $tag,string $url,$time, int $total){
        // create request in db
        R::exec("INSERT INTO `request`(`id`,`domain_id`,`url_id`,`element_id`,`total_time`,`send_time`,`total`) VALUES (NULL,?,?,?,?,CURRENT_TIME(),?)",
        [ 
            $this->getDomain($host)->id,
            $this->getUrl($url)->id,
            $this->getElement($tag)->id,
            $time,
            $total
        ]  //create request in db 
    );
    }
    public function addUrl(string $url){
        R::exec("INSERT INTO `url`(`id`,`name`) VALUES (NULL, ?)",[$url]); //create new url in db
    }
    public function addDomain(string $host){
        R::exec("INSERT INTO `domains`(`id`,`name`) VALUES (NULL, ?)",[$host]); //create new domain in db
    }
    public function addElement(string $tag){
        R::exec("INSERT INTO `elements`(`id`,`name`) VALUES (NULL, ?)",[$tag]); // create new element in db
       
    }
    public function getUrl(string $url){
        return R::findOne("url","name = ?",[$url]); //find url in url table
    }
    public function getElement(string $tag){

        return R::findOne("elements","name = ?",[$tag]);//find element in elements table
    }
    public function getDomain(string $host){
        return R::findOne("domains","name = ?",[$host]);//find domain in domains table
    }
    
}
//using pattern Singleton to our DataBaseObject
class DataBaseConnect {
    use DataBaseExecution;
    private static $object = [];

    protected function __construct(){
        /**Dont create this obj from new */
    }
    protected function __clone()
    {
     /** Dont clone this object */   
    }
    protected function __wakeup()
    {
        throw new Exception("Error");
    }
    public static function getObject(){
        $class = static::class;
        if(!isset(self::$object[$class])){ // check if object is isset
            self::$object[$class]=new static(); // create new object
            R::setup( 'mysql:host=localhost;dbname=domains','kobka', '9H0fa2Lbb90iKmaY' );//create connection into db
        }
        return self::$object[$class];//return object
    }
    //validation function protect us for duplicate data in db whithout Duplication Exeption
    public function checkVariables(string $host,string $tag,string $url){
        $obj = self::getObject();
        if(empty($obj->getElement($tag))){ // If we dont have <html> element in db we create new elem ;
           $obj->addElement($tag);
        }
        if(empty($obj->getDomain($host))){ // this same but for domain
            $obj->addDomain($host);
        }
        if(empty($obj->getUrl($url))){//this same but for url
            $obj->addUrl($url);
        }
        
    }
    
}


?>