<?php
/**
 * Simple directVPS API class
 * 
 * Initialize:
 * 	The 3rd paramenter is optional, but verifies the remote server. The first 2 parameters are to verify your server and gain access. 
 * 
 * $directvps = new directvps('/path/to/client.crt','/path/to/client.key','/path/to/ca.crt');
 * 
 * GET Request
 * 
 * $vpslist = $directvps->go('get_vpslist');
 * 
 * POST Request
 * 
 * $input = array(
 *		array(
 *			"productid" => "1", 
 *			"tag" => "Testserver", 
 *			"locationid" => "1", 
 *			"imageid" => "2", 
 *			"kernelid" => "2", 
 *			"type"  => "1", 
 *			"password" => 'E3nV31liGp@s$W0rD', // is between single quotes because of the $ in the string
 *			"sshkey" => "ssh-rsa AAAAB3Nz[…]zwow==", 
 *			"hostname" => "vps123.eigendomein.nl" 
 *		)
 *	);
 * $newvps = $directvps->go('add_vps', $input);
 *
 */
		
class directvps {

	private $cert;
	private $key;
	private $verification = false;
	public $url = 'https://api.directvps.nl/1/';
	public $debug = false;
	function __construct($cert, $key, $verification = false){
		if(!is_readable($cert)) {
			die('Geen cert gevonden');
		}
		if(!is_readable($key))
		{
			die('Geen key gevonden!');
		}
	
		$this->cert = $cert;
		
		$this->key = $key;
		
		if($verification) $this->verification = $verification;
	}
	
	private function jsonparams($array){
		return 'json=' . urlencode( json_encode($array) );
	}

	private function run($settings){
		$ch = curl_init();
		
		$defaults = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSLCERT => $this->cert,
			CURLOPT_SSLKEY => $this->key
		);
		
		if($this->verification){
			$defaults[CURLOPT_SSL_VERIFYPEER] = true;
			$defaults[CURLOPT_CAINFO] = $this->verification;
		}
		
		foreach($settings as $key => $value)
		{
			$defaults[$key] = $value;
		}

		if(!isset($defaults[CURLOPT_URL])){
			return false;
		}

		foreach($defaults as $option => $value){
			curl_setopt( $ch, $option , $value );
		}

		if($this->debug) {
			curl_setopt($ch, CURLOPT_HEADER, true); 
			curl_setopt($ch, CURLOPT_VERBOSE, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		}
		$response = curl_exec($ch);
	
		 if (curl_error($ch)) {
        printf("Error %s: %s", curl_errno($ch), curl_error($ch));
    }
		
	
		curl_close($ch);
		return json_decode($response);
	}

	public function go($action, $data = false){
		if($data){
			return $this->run(array(
				CURLOPT_URL => $this->url . $action,
				CURLOPT_POST => true, 
				CURLOPT_POSTFIELDS => $this->jsonparams( (array) $data)
			));
		}
		else {
			return $this->run(array(
				CURLOPT_URL => $this->url . $action
			));	
		}
	}
	
}

?>