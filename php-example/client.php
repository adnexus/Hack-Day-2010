<?php

class DW
{
	private $baseUrl;
	private $token;

	public function __construct($baseUrl)
	{
		$this->baseUrl = $baseUrl;
	}

	public function setToken($token)
	{
		$this->token = $token;
	}

	public function get($endpoint)
	{
		return $this->request($endpoint, 'GET');
	}
	
	public function post($endpoint, $data)
	{
		return $this->request($endpoint, 'POST', $data);
	}
	
	public function put($endpoint, $data)
	{
		return $this->request($endpoint, 'PUT', $data);
	}

	public function delete($endpoint, $data)
	{
		return $this->request($endpoint, 'DELETE');
	}
	
	private function request($endpoint, $method, $data=null)
	{
		$curlHandle = curl_init($this->baseUrl . $endpoint); 
		switch ($method) {
			case 'GET':
				// don't need to do anything
				break;
			case 'POST':
				curl_setopt($curlHandle, CURLOPT_POST, 1);                                
				curl_setopt($curlHandle, CURLOPT_POSTFIELDS, json_encode($data));
				break;
			case 'PUT':
				$jsonString = json_encode($data);
				$fh = fopen("php://memory", "w");
				fwrite($fh, $jsonString);
				rewind($fh);
				curl_setopt($curlHandle, CURLOPT_PUT, true);
				curl_setopt($curlHandle, CURLOPT_INFILE, $fh);
				curl_setopt($curlHandle, CURLOPT_INFILESIZE, strlen($jsonString));
				break;
			case 'DELETE':
				curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
		}
		curl_setopt($curlHandle, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curlHandle, CURLOPT_HEADER, 0);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);

		if ($this->token) {
			curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Authorization: ' . $this->token));
		}

		$rs = curl_exec($curlHandle);                                                  
		$resp = json_decode($rs, true);
		return $resp;
		if (isset($resp['response']['error'])) {
			$errorId = strtoupper($resp['response']['error']);
			throw new Exception($resp['response']['error_id'] . ' :: ' . $resp['response']['error']);
		} else if (isset($resp['response']['status']) && $resp['response']['status'] == 'OK') {
			return $resp;
		} else {
			throw new Exception('Request failed');                                     
		}                                                                            
	}
}
