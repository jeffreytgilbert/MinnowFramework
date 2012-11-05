<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

/**
 * This is a simple library for sending emails with Postmark created by Matthew Loberg (http://mloberg.com)
 */

final class Postmark{

	private $api_key;
	private $data = array();
	
	public function __construct($apikey, $from, $reply=""){
		$this->api_key = $apikey;
		$this->data["From"] = $from;
		$this->data["ReplyTo"] = $reply;
	}
	
	public function send(){
		$headers = array(
			"Accept: application/json",
			"Content-Type: application/json",
			"X-Postmark-Server-Token: {$this->api_key}"
		);
		$data = $this->data;
		$ch = curl_init('http://api.postmarkapp.com/email');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$return = curl_exec($ch);
		$curl_error = curl_error($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		// do some checking to make sure it sent
		if($http_code !== 200){
			return false;
		}else{
			return true;
		}
	}
	
	public function to($to){
		$this->data["To"] = $to;
		return $this;
	}
	
	public function subject($subject){
		$this->data["subject"] = $subject;
		return $this;
	}
	
	public function html_message($body){
		$this->data["HtmlBody"] = "<html><body>{$body}</body></html>";
		return $this;
	}
	
	public function plain_message($msg){
		$this->data["TextBody"] = $msg;
		return $this;
	}
	
	public function tag($tag){
		$this->data["Tag"] = $tag;
		return $this;
	}

}
