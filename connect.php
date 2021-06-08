<?php
//pass:: 9H0fa2Lbb90iKmaY
session_start(); 
require "rb.php";

trait DataBaseExecution {
   
    public function addRequest(string $host,string $tag,string $url,$time){
        R::exec("INSERT INTO `request`(`id`,`domain_id`,`url_id`,`element_id`,`total_time`,`send_time`) VALUES (NULL,?,?,?,?,CURRENT_TIME())",
        [ 
            $this->getDomain($host)->id,
            $this->getUrl($url)->id,
            $this->getElement($tag)->id,
            $time
        ]  
    );
    }
    public function addUrl(string $url){
        R::exec("INSERT INTO `url`(`id`,`name`) VALUES (NULL, ?)",[$url]);
    }
    public function addDomain(string $host){
        R::exec("INSERT INTO `domains`(`id`,`name`) VALUES (NULL, ?)",[$host]);
    }
    public function addElement(string $tag){
        R::exec("INSERT INTO `elements`(`id`,`name`) VALUES (NULL, ?)",[$tag]);
       
    }
    public function getUrl(string $url){
        return R::findOne("url","name = ?",[$url]);
    }
    public function getElement(string $tag){

        return R::findOne("elements","name = ?",[$tag]);
    }
    public function getDomain(string $host){
        return R::findOne("domains","name = ?",[$host]);
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
        if(!isset(self::$object[$class])){
            self::$object[$class]=new static();
            R::setup( 'mysql:host=localhost;dbname=domains','kobka', '9H0fa2Lbb90iKmaY' );
        }
        return self::$object[$class];
    }
    public function checkVariables(string $host,string $tag,string $url){
        $obj = self::getObject();
        if(empty($obj->getElement($tag))){
           $obj->addElement($tag);
        }
        if(empty($obj->getDomain($host))){
            $obj->addDomain($host);
        }
        if(empty($obj->getUrl($url))){
            $obj->addUrl($url);
        }
        
    }
    
}


?>