<?php
namespace common\components;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

use yii\base\Component;

class JAPI extends Component{
	const HTTP_METHOD_POST  = "POST";
	const HTTP_METHOD_GET  	= "GET";
	const HTTP_METHOD_DELETE= "DELETE";
	const HTTP_METHOD_PUT  	= "PUT";
	
	const RAW_DATA_TYPE_XML = 'application/xml'; 
	const RAW_DATA_TYPE_JSON = 'application/json';

	public $maxRedirects = 0;
	public $timeout = 30;
	public $keepalive = true;
	public $debug = false;
	public $baseAPIUrl = "";
	public $authKey = "";
	
	public function init(){
		
    }
    
    public function __construct(){ 
        
    }
    
	public function process($p_api_url,$p_method = JAPI::HTTP_METHOD_GET,$p_data = array(),$p_raw_data= null,$p_raw_data_type = null,$p_unmask_status = false){
		$client = new Client([
            'base_uri'=>$this->baseAPIUrl,
            'timeout'  => $this->timeout,
            'verify'=>false,
            'allow_redirects'=>[
                'max'       => $this->maxRedirects,
                //'strict'    => false,
                //'referer'   => true,
                //'protocols' => ['http', 'https']
            ],
            //proxy'           => '192.168.16.1:10',
        ]);
        //NOT HANDLED
        /*
         * 'keepalive'    => $this->keepalive
         *  $client->setUnmaskStatus($p_unmask_status);
         */
        $options = array();

        $p_method = ($p_method == JAPI::HTTP_METHOD_POST || $p_method == JAPI::HTTP_METHOD_DELETE || $p_method == JAPI::HTTP_METHOD_PUT)?$p_method:JAPI::HTTP_METHOD_GET;
		if($p_raw_data != null){
            $options["body"] = $p_raw_data;
			if($p_raw_data_type != null){
                $options["headers"]["Content-Type"] = $p_raw_data_type;
			}
		}
		if(is_array($p_data) && count($p_data) > 0){
            if($p_method == JAPI::HTTP_METHOD_POST || $p_method == JAPI::HTTP_METHOD_PUT){
                $options["form_params"] = $p_data;
            }else{
                $options["query"] = $p_data;
            }
		}

        $p_api_url = strrpos($p_api_url,"/") === 0 ? substr($p_api_url,1):$p_api_url;//REMOVING THE STARTING SLASH "/"
        try {
            $response = $client->request($p_method,$p_api_url, $options);
            if($response->getStatusCode() == 200){
                return $response->getBody()->getContents();
            }else{
                //TODO HANDLE ERRORS
                return null;
            }
        } catch (RequestException $e) {
            //print_r($e->getRequest()->getUri());die();
            if ($e->hasResponse()) {
                print_r($e->getResponse());
            }
        }
	}
}