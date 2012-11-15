<?php 

class SampleOptional extends DataObject{
	public function __construct($data=array()){
		$this->addAllowedData(array());
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof SampleOptional)?$DataObject:new SampleOptional($DataObject->toArray());
	}	
}


class SampleOptionalCollection extends DataCollection{
	public function __construct(){
		$this->setCollectionType('SampleOptional');
	}
	
	public static function cast(SampleOptionalCollection $ObjectNameCollection){
		return $ObjectNameCollection;
	}
	
}