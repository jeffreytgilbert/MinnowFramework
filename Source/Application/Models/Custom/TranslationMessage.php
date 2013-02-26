<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class TranslationMessage extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'page_address'=>DataType::TEXT,
			'message_key'=>DataType::TEXT,
			'created_datetime'=>DataType::DATETIME,
			'en'=>DataType::TEXT,
			'ab'=>DataType::TEXT,
			'aa'=>DataType::TEXT,
			'af'=>DataType::TEXT,
			'ak'=>DataType::TEXT,
			'sq'=>DataType::TEXT,
			'am'=>DataType::TEXT,
			'ar'=>DataType::TEXT,
			'an'=>DataType::TEXT,
			'hy'=>DataType::TEXT,
			'as'=>DataType::TEXT,
			'av'=>DataType::TEXT,
			'ae'=>DataType::TEXT,
			'ay'=>DataType::TEXT,
			'az'=>DataType::TEXT,
			'bm'=>DataType::TEXT,
			'ba'=>DataType::TEXT,
			'eu'=>DataType::TEXT,
			'be'=>DataType::TEXT,
			'bn'=>DataType::TEXT,
			'bh'=>DataType::TEXT,
			'bi'=>DataType::TEXT,
			'bs'=>DataType::TEXT,
			'br'=>DataType::TEXT,
			'bg'=>DataType::TEXT,
			'my'=>DataType::TEXT,
			'ca'=>DataType::TEXT,
			'ch'=>DataType::TEXT,
			'ce'=>DataType::TEXT,
			'ny'=>DataType::TEXT,
			'zh'=>DataType::TEXT,
			'cv'=>DataType::TEXT,
			'kw'=>DataType::TEXT,
			'co'=>DataType::TEXT,
			'cr'=>DataType::TEXT,
			'hr'=>DataType::TEXT,
			'cs'=>DataType::TEXT,
			'da'=>DataType::TEXT,
			'dv'=>DataType::TEXT,
			'nl'=>DataType::TEXT,
			'dz'=>DataType::TEXT,
			'eo'=>DataType::TEXT,
			'et'=>DataType::TEXT,
			'ee'=>DataType::TEXT,
			'fo'=>DataType::TEXT,
			'fj'=>DataType::TEXT,
			'fi'=>DataType::TEXT,
			'fr'=>DataType::TEXT,
			'ff'=>DataType::TEXT,
			'gl'=>DataType::TEXT,
			'ka'=>DataType::TEXT,
			'de'=>DataType::TEXT,
			'el'=>DataType::TEXT,
			'gn'=>DataType::TEXT,
			'gu'=>DataType::TEXT,
			'ht'=>DataType::TEXT,
			'ha'=>DataType::TEXT,
			'he'=>DataType::TEXT,
			'hz'=>DataType::TEXT,
			'hi'=>DataType::TEXT,
			'ho'=>DataType::TEXT,
			'hu'=>DataType::TEXT,
			'ia'=>DataType::TEXT,
			'id'=>DataType::TEXT,
			'ie'=>DataType::TEXT,
			'ga'=>DataType::TEXT,
			'ig'=>DataType::TEXT,
			'ik'=>DataType::TEXT,
			'io'=>DataType::TEXT,
			'is'=>DataType::TEXT,
			'it'=>DataType::TEXT,
			'iu'=>DataType::TEXT,
			'ja'=>DataType::TEXT,
			'jv'=>DataType::TEXT,
			'kl'=>DataType::TEXT,
			'kn'=>DataType::TEXT,
			'kr'=>DataType::TEXT,
			'ks'=>DataType::TEXT,
			'kk'=>DataType::TEXT,
			'km'=>DataType::TEXT,
			'ki'=>DataType::TEXT,
			'rw'=>DataType::TEXT,
			'ky'=>DataType::TEXT,
			'kv'=>DataType::TEXT,
			'kg'=>DataType::TEXT,
			'ko'=>DataType::TEXT,
			'ku'=>DataType::TEXT,
			'kj'=>DataType::TEXT,
			'la'=>DataType::TEXT,
			'lb'=>DataType::TEXT,
			'lg'=>DataType::TEXT,
			'li'=>DataType::TEXT,
			'ln'=>DataType::TEXT,
			'lo'=>DataType::TEXT,
			'lt'=>DataType::TEXT,
			'lu'=>DataType::TEXT,
			'lv'=>DataType::TEXT,
			'gv'=>DataType::TEXT,
			'mk'=>DataType::TEXT,
			'mg'=>DataType::TEXT,
			'ms'=>DataType::TEXT,
			'ml'=>DataType::TEXT,
			'mt'=>DataType::TEXT,
			'mi'=>DataType::TEXT,
			'mr'=>DataType::TEXT,
			'mh'=>DataType::TEXT,
			'mn'=>DataType::TEXT,
			'na'=>DataType::TEXT,
			'nv'=>DataType::TEXT,
			'nb'=>DataType::TEXT,
			'nd'=>DataType::TEXT,
			'ne'=>DataType::TEXT,
			'ng'=>DataType::TEXT,
			'nn'=>DataType::TEXT,
			'no'=>DataType::TEXT,
			'ii'=>DataType::TEXT,
			'nr'=>DataType::TEXT,
			'oc'=>DataType::TEXT,
			'oj'=>DataType::TEXT,
			'cu'=>DataType::TEXT,
			'om'=>DataType::TEXT,
			'or'=>DataType::TEXT,
			'os'=>DataType::TEXT,
			'pa'=>DataType::TEXT,
			'pi'=>DataType::TEXT,
			'fa'=>DataType::TEXT,
			'pl'=>DataType::TEXT,
			'ps'=>DataType::TEXT,
			'pt'=>DataType::TEXT,
			'qu'=>DataType::TEXT,
			'rm'=>DataType::TEXT,
			'rn'=>DataType::TEXT,
			'ro'=>DataType::TEXT,
			'ru'=>DataType::TEXT,
			'sa'=>DataType::TEXT,
			'sc'=>DataType::TEXT,
			'sd'=>DataType::TEXT,
			'se'=>DataType::TEXT,
			'sm'=>DataType::TEXT,
			'sg'=>DataType::TEXT,
			'sr'=>DataType::TEXT,
			'gd'=>DataType::TEXT,
			'sn'=>DataType::TEXT,
			'si'=>DataType::TEXT,
			'sk'=>DataType::TEXT,
			'sl'=>DataType::TEXT,
			'so'=>DataType::TEXT,
			'st'=>DataType::TEXT,
			'es'=>DataType::TEXT,
			'su'=>DataType::TEXT,
			'sw'=>DataType::TEXT,
			'ss'=>DataType::TEXT,
			'sv'=>DataType::TEXT,
			'ta'=>DataType::TEXT,
			'te'=>DataType::TEXT,
			'tg'=>DataType::TEXT,
			'th'=>DataType::TEXT,
			'ti'=>DataType::TEXT,
			'bo'=>DataType::TEXT,
			'tk'=>DataType::TEXT,
			'tl'=>DataType::TEXT,
			'tn'=>DataType::TEXT,
			'to'=>DataType::TEXT,
			'tr'=>DataType::TEXT,
			'ts'=>DataType::TEXT,
			'tt'=>DataType::TEXT,
			'tw'=>DataType::TEXT,
			'ty'=>DataType::TEXT,
			'ug'=>DataType::TEXT,
			'uk'=>DataType::TEXT,
			'ur'=>DataType::TEXT,
			'uz'=>DataType::TEXT,
			've'=>DataType::TEXT,
			'vi'=>DataType::TEXT,
			'vo'=>DataType::TEXT,
			'wa'=>DataType::TEXT,
			'cy'=>DataType::TEXT,
			'wo'=>DataType::TEXT,
			'fy'=>DataType::TEXT,
			'xh'=>DataType::TEXT,
			'yi'=>DataType::TEXT,
			'yo'=>DataType::TEXT,
			'za'=>DataType::TEXT,
			'zu'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof TranslationMessage)?$DataObject:new TranslationMessage($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
}

class TranslationMessageCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('TranslationMessage');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getTranslationMessageByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof TranslationMessage)?$return:new TranslationMessage($return->toArray());
	}
	
	public function getTranslationMessageByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof TranslationMessage)?$return:new TranslationMessage($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}