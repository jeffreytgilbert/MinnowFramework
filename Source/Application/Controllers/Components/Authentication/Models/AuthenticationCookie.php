<?php 


class AuthenticationCookie extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_id'=>DataType::NUMBER,
			'created_datetime'=>DataType::DATETIME,
			'ip'=>DataType::TEXT,
			'proxy'=>DataType::TEXT,
			'user_agent'=>DataType::TEXT,
			'access_token'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}
	
	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof AuthenticationCookie)?$DataObject:new AuthenticationCookie($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	public function getUserId(){ return $this->getInteger('user_id'); }
	public function getLoginTime(){ return $this->getDateTimeObject('login_time'); }
// 	public function getLastAccess(){ return $this->getDateTimeObject('last_access'); }
	public function getIp(){ return $this->getString('ip'); }
	public function getProxy(){ return $this->getString('proxy'); }
	public function getUserAgent(){ return $this->getString('user_agent'); }
	public function getAccessToken(){ return $this->getString('access_token'); }
	
}