<?php

class FormField extends DataObject{
	public function __construct($data=array(), $default_filter='Parse::decode'){
		$this->addAllowedData(array(
			'field_id'=>DataType::NUMBER,
			'form_id'=>DataType::NUMBER,
			'form_name'=>DataType::TEXT,
			'form_title'=>DataType::TEXT,
			'submit_label'=>DataType::TEXT,
			'user_id'=>DataType::NUMBER,
			'created_datetime'=>DataType::DATE,
			'modified_datetime'=>DataType::DATE,
			'detail'=>DataType::TEXT,
			'hint'=>DataType::TEXT,
			'answers'=>DataType::TEXT,
			'explanation'=>DataType::TEXT,
			'options'=>DataType::TEXT,
			'maxlength'=>DataType::NUMBER,
			'input_type'=>DataType::TEXT,
			'input_width'=>DataType::TEXT,
			'span_width'=>DataType::TEXT,
			'line_width'=>DataType::TEXT,
			'line_alignment'=>DataType::TEXT,
			'is_manditory'=>DataType::NUMBER,
			'approved_by'=>DataType::NUMBER,
			'is_disabled'=>DataType::NUMBER,
			'order_id'=>DataType::NUMBER
		),true);
		parent::__construct($data, $default_filter);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof FormField)?$DataObject:new FormField($DataObject->toArray());
	}
	
}

class FormFieldCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('FormField');
		parent::__construct($array_of_objects);
	}	
}