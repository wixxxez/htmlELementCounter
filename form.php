<?php
require "connect.php"; 
class HostService {
    public function getHostName(string $str):string {
        if(preg_match('@^(?:https://)?([^/]+)@i',$str, $matches)){
            
            return $matches[0];    
        }
        return "null";
    }
}
class ValidationService {

    public function validateUrl(string $str):string {
        if(preg_match("/^http/",$str)){
            return true;
        }
        return false;
    }
    public function validateTag(string $str):string {
        if(preg_match("/<.+?>/",$str)){
            return true;
        }
        return false;
    }
    
}
class CurlService {
    private $curl;
    public function __construct(string $url){
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);

        return curl_exec($this->curl);
        
    }
    public function getTotalTime(){
        $time = curl_getinfo($this->curl);
        return $time["total_time"];
    }
    public function getCode(){
        $code = curl_getinfo($this->curl);
        return $code['http_code'];
    }
    public function getCurl(){
        return curl_exec($this->curl);
    }
}
class ParseHtmlService {
    private $document;
    public function __construct($html){
        $this->document = new DOMDocument();
        @ $this->document->loadHTML($html);
        return $this;
    }
    public function findHtmlTag(string $tag){
        $tag = str_replace("<","",$tag);
        $tag = str_replace(">","",$tag);
        return $this->document->getElementsByTagName($tag)->length;
    }
}
class AjaxController {
    private $validationService;
    private $HostService;
    public function __construct(HostService $host, ValidationService $service)
    {
        $this->validationService = $service;
        $this->HostService = $host;
    }
    public function index() {
        $url = $_POST['url'];
        $tag = $_POST['tag'];
        $errors=[];
        if($this->validationService->validateUrl($url)){
            $host =$this->HostService->getHostName($url);
            echo "Domen: ".$host;
            if($this->validationService->validateTag($tag)){
                $curl = new CurlService($url);
                
                if($curl->getCode()==200){
                    $time = $curl->getTotalTime();
                    $document = new ParseHtmlService($curl->getCurl());
                    echo "\nLen: ".$document->findHtmlTag($tag);
                    echo "\nTotal time: ".$time;
                    DataBaseConnect::getObject()->checkVariables($host,$tag,$url);
                    DataBaseConnect::getObject()->addRequest($host,$tag,$url,$time);
                    
                }
                if($curl->getCode()==301) {
                    echo "Url need to be: 'https://www.example.com'";
                }
                if($curl->getCode()==0) {
                    echo "Bad Url";
                }
                if($curl->getCode()==404){
                    echo "Url not found";
                }
                
            }
            else {
                $errors[]= "Invalid html tag";
            }
        }
        else {
            $errors[] =  "Invalid URL";
        }
       
       
        if(!empty($errors)){
            
            echo $errors[0];
        }
    }
}
$ajax = new AjaxController(new HostService,new ValidationService);
$ajax->index();
