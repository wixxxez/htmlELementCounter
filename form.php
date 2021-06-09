<?php
//created by Artem Herasymiuk
error_reporting(0);

require "connect.php";  // our file with DataBaseConnect class
class HostService { // Something logic with host
    public function getHostName(string $str):string {
        // This regular expression return domain name (https://www.google.com) or null (but after all validations func we always have a correct domain))
        if(preg_match('@^(?:https://)?([^/]+)@i',$str, $matches)){
            
            return $matches[0];    
        }
        return "null";
    }
    public function showStatistic(string $host, string $tag,bool $status){//This function show Statistic
        // Get all urls from  domain
        echo "<br/>"."<hr>"."General statistic"."<br/>";
        $urls = R::getAll(" SELECT COUNT(DISTINCT request.url_id) FROM request INNER JOIN domains ON request.domain_id = domains.id WHERE domains.name = ?",[ $host ]);
        echo "<br/>".$urls[0]["COUNT(DISTINCT request.url_id)"]." different URLs ".$host." have been fetched";
        //Get average time for all request in one domain the last 24 hours
        $avg = R::getAll("SELECT AVG(request.total_time) FROM request INNER JOIN domains ON request.domain_id = domains.id WHERE domains.name = ? AND request.send_time >= NOW() - INTERVAL 1 DAY",[$host]);
        echo "<br/>Average fetch time from ".$host." during the last 24 hours: ".round($avg[0]["AVG(request.total_time)"],3)."s";
        //Get sum of all elements in domain
        if($status){
            $HostElements = R::getAll("SELECT SUM(request.total) FROM request INNER JOIN elements ON request.element_id = elements.id INNER JOIN domains ON request.domain_id = domains.id WHERE elements.name = ? AND domains.name = ?", [$tag,$host] );
            echo "<br/>There was a total of ".$HostElements[0]['SUM(request.total)'] ." ".$tag." elements from ".$host;
            //Get sum of all elements of all request
            $totalElements = R::getAll("SELECT SUM(request.total) FROM request INNER JOIN elements ON request.element_id = elements.id WHERE elements.name = ?",[ $tag ]);
            echo "<br/>Total of ".$totalElements[0]["SUM(request.total)"]." ".$tag." elements counted in all requests ever made.";
        }
    }
}
class ValidationService {

    public function validateUrl(string $str):string {
        //this reqular expression validate a correct url (http/https://example)
        if(preg_match("/^http/",$str)){
            return true;
        }
        return false;
    }
    public function getTag(string $tag){
        $tag = str_replace("<","",$tag);
        $tag = str_replace(">","",$tag);
        return $tag;
    }
    //This function return previos request of last 5 minuts or null
    public function checkLastRequest(string $tag,string $url){
        // this sql return a last request with passed tag and url
        $r = R::getAll("SELECT request.* FROM request INNER JOIN elements ON request.element_id=elements.id INNER JOIN url ON request.url_id = url.id WHERE url.name = ? AND elements.name = ? ORDER BY request.id DESC LIMIT 1", [$url, $tag]);
    
        $now = new DateTime('now'); // Actual date time
        $time =  new DateTime($r[0]['send_time']);//Sending last request time
        $timeDiffernce = $now->diff($time); // find time difference
        if($timeDiffernce->i < 5){ // if time difference less that 5 minute we return previos request
            return $r;
        }
        else {
            return null; // return null if time difference more that 5 minutes
        }
    }
    
}
// this service responsible for sending curl
class CurlService {
    private $curl;
    public function __construct(string $url){ // pass url to constructor and create curl
        $this->curl = curl_init(); // curl start
        //config curl
        curl_setopt($this->curl, CURLOPT_URL, $url); 
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        //return response of curl
        return curl_exec($this->curl);
        
    }
    // return total time for our curl
    public function getTotalTime(){
        $time = curl_getinfo($this->curl);
        return $time["total_time"];
    }
    // return http code from url
    public function getCode(){
        $code = curl_getinfo($this->curl);
        return $code['http_code'];
    }
    //return curl object
    public function getCurl(){
        return curl_exec($this->curl);
    }
}
//this service resbonsible for find information in html
class ParseHtmlService {
    private $document;
    public function __construct($html){//pass curl response here 
        
        $this->document = new DOMDocument(); // create DOM object to parse him in future
        @ $this->document->loadHTML($html); // load html to DOM object
        return $this;
    }
    
    public function findHtmlTag(string $tag){
        
        // return len elements on page;
        return $this->document->getElementsByTagName($tag)->length;
    }
}
// This Controller responsible for send response to ajax form
class AjaxController {
    private $validationService;
    private $HostService;
    //upload service
    public function __construct(HostService $host, ValidationService $service)
    {
        $this->validationService = $service;
        $this->HostService = $host;
    }
    public function index() {
        //get data from form
        $url = $_POST['url'];
        
        $tag = $this->validationService->getTag($_POST['tag']);
        
        //First Level: validate url
        if($this->validationService->validateUrl($url)){
            
            $host =$this->HostService->getHostName($url);//get domain name
            if($host=="null"){
                echo "<br/>Cannot find host";//show error when we cannot find domain name
            }
            else {//go to next level
                
               
                    $curl = new CurlService($url);//create curl request
                    $code = $curl->getCode(); // get https code 
                    switch($code){ //  use switch to make different behaviour for different code

                    
                    case(200): // Good code we can go to 3 level
                        echo "<br/>Domain: ".$host;
                        $status = false; // using status to change statistic information i think it was better that using Builder 
                        $db = DataBaseConnect::getObject();//create bd class
                        $time = $curl->getTotalTime(); // get time of curl response
                        $document = new ParseHtmlService($curl->getCurl()); // find elements
                        $elements = $document->findHtmlTag($tag);
                        
                        $oldRequest = $this->validationService->checkLastRequest($tag,$url);// check if we send this same request of last five minute
                        if($oldRequest==null){ // Create new request
                            
                            echo "<br/>Elements: ".$elements; // echo len of elements
                            echo "<br/>Total time: ".$time; // Total time
                            echo "<br/>Send time is: ".date("Y-m-d H:i:s");
                            if($elements!=0){ // this protect us from spamming data to database and add only true html elements
                                $status = true;
                                $db->checkVariables($host,$tag,$url); // Check variables and add him into database if we used this data first time 
                                $db->addRequest($host,$tag,$url,$time,$document->findHtmlTag($tag)); // Add request into db
                            }
                            
                        }
                        else { // Show old request
                            $status = true;
                            echo "<br/>Elements: ".$oldRequest[0]['total'];
                            echo "<br/>Total time: ".$oldRequest[0]['total_time'];
                            echo "<br/>Send time is: ".$oldRequest[0]['send_time'];
                        }
                        $this->HostService->showStatistic($host,$tag,$status);// Show general statistic
                        break;
                    
                    case(301) : //Bad url code 
                        
                        $_POST['url'] = substr($_POST['url'],0,8)."www.".substr($_POST['url'],8);
                       
                        $this->index();//create redirect to www;
                        echo "<br/>Url need to be: 'https://www.example.com'";// Alert message
                        break;
                    case(0) : // This code it's header exeption "Allow Origin Header"
                        echo "<br/>Bad Url";
                        break;
                    case(404): // Not found this page
                        echo "<br/>Url not found";
                        break;
                    default: // And when we have another code
                        echo "<br/>Something wrong";
                        break;
                    }
                
            }
        }        else {
            echo "<br/>Invalid URL";
        }
       
    }
}
$ajax = new AjaxController(new HostService,new ValidationService);//create controller object
$ajax->index();// call main function
