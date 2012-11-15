<?php 

class SampleRequired extends DataObject{
	public function __construct($data=array()){
		$this->addAllowedData(array());
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof SampleRequired)?$DataObject:new SampleRequired($DataObject->toArray());
	}	
}


class SampleRequiredCollection extends DataCollection{
	public function __construct(){
		$this->setCollectionType('SampleRequired');
	}
	
	public static function cast(SampleRequiredCollection $ObjectNameCollection){
		return $ObjectNameCollection;
	}
	
}