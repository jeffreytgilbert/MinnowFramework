<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class FormBuilderPage extends PageController implements HTMLCapable, JSONCapable{

	public 
		$forms,
		$dummy_forms;
	
	public function handleRequest(){
		
		// ----- Page builder & Sitemap -----
		// Grab all controller files into an array
		// Check the db to see if they exist in the sitemap
		// Insert the ones that dont exist already
		// Requery the database for the ones that exist
		// Put them in a data field so they can be used on the view
		
		// ----- Models & Actions -----
		// Query the database to see which tables it has
		// Check the file system to see which files are missing, and build a collection of them for the view
		
		// ----- Form / View builder -----
		// ... tbd
		
		$DataModelForm = $this->getForm('DataModel');
		$DataActionForm = $this->getForm('ActionModel');
		$FormBuilderForm = $this->getForm('FormBuilder');
		
		// Check for form login from local page request
		// If data exists in expected form, check it as a login against the db
		if($PageBuilderForm->hasBeenSubmitted()){
			
			// Handle errors one at a time (if you want to handle them all at the same time, throw each one in a try catch block of its own
			$errors = array();
			
			try{
				$PageBuilderForm->checkString('path')->required()->validate();
				$PageBuilderForm->checkWords('description')->required()->allowUTF8WordsAndNumbersAndPunctuation(false)->maxLength(160);
				
			} catch(Exception $e){
				$errors = $PageBuilderForm->getCurrentErrors();
			}
			
			if(count($errors) == 0){
				
				// $UserLogin = UserLoginActions::selectByUniqueIdentifierAndProviderTypeId($PageBuilderForm->getFieldData('unique_identifier'), 1);
				
			} else {
				foreach($errors as $field => $error){
					if(key($error) != ''){
						$this->flashError($field,$field.': '.key($error));
					}
				}
			}
		}
		
	}
	
	public function renderJSON(){ return $this->output = parent::renderJSON(); }
	public function renderHTML(){ return $this->_page_body = parent::renderHTML(); }
	
	private function buildModel(){
		$RuntimeInfo = RuntimeInfo::instance();
		$db = $RuntimeInfo->connections()->MySQL();
				
		$blank_model = file_get_contents(Path::toFramework().File::osPath('Scaffolding/Model.txt'));
		
		$table_names = array();
		$object_names = array();
		$db->query('SHOW TABLES');
		$x=0;
		while($db->readRow()){
			$table_name = array_pop($db->row_data);
			$table_names[$x] = $table_name;
			$object_name_parts = explode('_',$table_name);
			foreach($object_name_parts as $key => $object_name_part){
				$object_name_parts[$key] = ucfirst($object_name_part);
			}
			$object_names[$x] = implode('',$object_name_parts);
			$x++;
		}
		
		echo '
		<h3>Create objects for:</h3>
		<form method="POST" action="?">
		<ul>';
		foreach($table_names as $key => $table_name){
			
			$object_fields = array();
			$db->query('DESC '.$table_name);
			
			while($db->readRow()){
				$split_pos = stripos($db->row_data['Type'], '(');
				if($split_pos > 0){
					$type = substr($db->row_data['Type'],0,$split_pos);
				} else {
					$type = $db->row_data['Type'];
				}

				if(substr($db->row_data['Field'],0,3) == 'is_'){
					$object_fields[$db->row_data['Field']] = 'DataType::BOOLEAN';
					continue;
				}
				
				switch($type){
					case 'enum': 
					case 'char': 
					case 'varchar': 
					case 'text': 
					case 'tinytext': 
					case 'mediumtext': 
					case 'longtext': 
					case 'set': 
						$object_fields[$db->row_data['Field']] = 'DataType::TEXT';
					break;
					case 'blob': 
					case 'varblob': 
					case 'tinyblob': 
					case 'mediumblob': 
					case 'longblob': 
//					case 'clob': 
					case 'binary': 
					case 'varbinary': 
						$object_fields[$db->row_data['Field']] = 'DataType::BINARY';
					break;
					case 'bit': 
					case 'bool': 
					case 'boolean': 
						$object_fields[$db->row_data['Field']] = 'DataType::BOOLEAN';
					break;
					case 'int': 
					case 'tinyint': 
					case 'smallint': 
					case 'mediumint': 
					case 'integer': 
					case 'bigint': 
					case 'decimal': 
					case 'dec': 
					case 'float': 
					case 'double': 
					case 'double precision': 
					case 'serial': 
					case 'number': 
						$object_fields[$db->row_data['Field']] = 'DataType::NUMBER';
					break;
					case 'date': 
					case 'year': 
						$object_fields[$db->row_data['Field']] = 'DataType::DATE';
					break;
					case 'datetime': 
						$object_fields[$db->row_data['Field']] = 'DataType::DATETIME';
					break;
					case 'time': 
						$object_fields[$db->row_data['Field']] = 'DataType::TIME';
					break;
					case 'timestamp': 
						$object_fields[$db->row_data['Field']] = 'DataType::TIMESTAMP';
					break;
				}
			}
			
			$file_path = File::osPath(Path::toModels().'/Custom/'.$object_names[$key].'.php');
			
			if(	isset($_POST['list']) && 
				in_array($object_names[$key],array_keys($_POST['list'])) && 
				$_POST['list'][$object_names[$key]] == 1 &&
				!file_exists($file_path)){
				
				$buffer = array();
				
				// for storing the objects associated with keys of this type
				$foreign_object_names = array();
				
				foreach($object_fields as $field => $type){
					// check to see if they're IDs and if they are, add objects to them
					$buffer[] = "'{$field}'=>{$type}";
					if(lower(substr($field,-3)) == '_id' && substr($field,0,-3) != $table_name){
						$key_name = substr($field,0,-3);
						$name_parts = explode('_',$key_name);
						$foreign_object_name = '';
						foreach($name_parts as $name_part){
							$foreign_object_name .= ucfirst($name_part);
						}
						
						$buffer[] = "'{$foreign_object_name}'=>DataType::OBJECT";
						$foreign_object_names[] = $foreign_object_name;
					}
				}
				
				// Replace all the ObjectName occurances with the actual objects name
				$model = str_replace('ObjectName', $object_names[$key], $blank_model);
				
				// add column names to the array
				$model = str_replace('(array());', '(array(
			'.implode(",\n\t\t\t",$buffer).'
		),true);', $model);
				
				// add any foreign key'd object requests into the model
				$keyed_objects = '';
				foreach($foreign_object_names as $foreign_object_name){
					$keyed_objects .= '
	public function get'.$foreign_object_name.'(){
		return ($this->getObject(\''.$foreign_object_name.'\') instanceof '.$foreign_object_name.')
			?$this->_data[\''.$foreign_object_name.'\']
			:new '.$foreign_object_name.'();
	}
	';
				}
				$model = str_replace('/* Foreign Keyed Object Calls */', $keyed_objects, $model);
				
				file_put_contents($file_path, $model);
				chmod($file_path, 0755);
				
				echo '<li>Created '.$file_path.'</li>';
				echo '<li><pre>'.htmlentities($model).'</pre></li>';
			}
			
			if(file_exists($file_path)){
				echo '<li>'.$object_names[$key].' exists</li>';
			} else {
				echo '<li><input type="checkbox" name="list['.$object_names[$key].']" value="1">'.$object_names[$key].'</li>';
			}
		}
		echo '
		</ul>
		<input type="submit" value="create">
		</form>
		';
	}
	
	private function buildAction(){
				$RuntimeInfo = RuntimeInfo::instance();
		$db = $RuntimeInfo->connections()->MySQL();
		
		$blank_action = file_get_contents(Path::toFramework().File::osPath('Scaffolding/Action.txt'));
		
		$table_names = array();
		$object_names = array();
		$db->query('SHOW TABLES');
		$x=0;
		while($db->readRow()){
			$table_name = array_pop($db->row_data);
			$table_names[$x] = $table_name;
			$object_name_parts = explode('_',$table_name);
			foreach($object_name_parts as $key => $object_name_part){
				$object_name_parts[$key] = ucfirst($object_name_part);
			}
			$object_names[$x] = implode('',$object_name_parts);
			$x++;
		}
		
		echo '
		<h3>Create actions for:</h3>
		<form method="POST" action="?">
		<ul>';
		foreach($table_names as $key => $table_name){
			
			$object_fields = array();
			
			$int_array = array(); // all integers must be handled differently for proper sanitizing and math functionality
			$boolean_array = array(); // match all columns with is_ as the first bit
			$string_array = array(); 
			$binary_array = array(); 
			$date_array = array(); 
			$datetime_array = array(); 
			$time_array = array(); 
			
			$primary_key = 'id'; // if table_name_id is matched, uses this as primary key. default to id even if it doesnt exist. Query would error anyway
			$update_columns = array(); // contains modified_datetime if it exists
			$insert_columns = array(); // contains created_datetime if it exists
			
			// crud
			$insert = '';
			$list = '';
			$select = '';
			$update = '';
			$delete = '';
			
			$db->query('DESC '.$table_name);
			
			$first_id_found = false;
			
			while($db->readRow()){
				$split_pos = stripos($db->row_data['Type'], '(');
				if($split_pos > 0){
					$type = substr($db->row_data['Type'],0,$split_pos);
				} else {
					$type = $db->row_data['Type'];
				}
				
				if( $db->row_data['Field'] == $table_name.'_id' || 
					$db->row_data['Field'] == 'id' || 
					($first_id_found == false && substr($db->row_data['Field'],-3) == '_id') // use the first id found as the primary key
					){
					$primary_key = $db->row_data['Field'];
					$first_id_found = true;
				} else {
					if($db->row_data['Field'] == 'created_datetime' || $db->row_data['Field'] == 'created'){
						$insert_columns[] = $db->row_data['Field'];
					} else if($db->row_data['Field'] == 'modified_datetime' || $db->row_data['Field'] == 'modified') {
						$update_columns[] = $db->row_data['Field'];
					} else {
						$insert_columns[] = $db->row_data['Field'];
						$update_columns[] = $db->row_data['Field'];
					}
					
					if(substr($db->row_data['Field'],0,3) == 'is_'){
						$object_fields[$db->row_data['Field']] = 'boolean';
						$boolean_array[] = $db->row_data['Field'];
						continue;
					}
				}
				
				switch($type){
					case 'enum': 
					case 'char': 
					case 'varchar': 
					case 'text': 
					case 'tinytext': 
					case 'mediumtext': 
					case 'longtext': 
					case 'set': 
						$object_fields[$db->row_data['Field']] = 'text';
						$string_array[] = $db->row_data['Field'];
					break;
					case 'blob': 
					case 'varblob': 
					case 'tinyblob': 
					case 'mediumblob': 
					case 'longblob': 
//					case 'clob': 
					case 'binary': 
					case 'varbinary': 
						$object_fields[$db->row_data['Field']] = 'binary';
						$binary_array[] = $db->row_data['Field'];
					break;
					case 'bit': 
					case 'bool': 
					case 'boolean': 
						$object_fields[$db->row_data['Field']] = 'boolean';
						$boolean_array[] = $db->row_data['Field'];
					break;
					case 'int': 
					case 'tinyint': 
					case 'smallint': 
					case 'mediumint': 
					case 'integer': 
					case 'bigint': 
					case 'decimal': 
					case 'dec': 
					case 'float': 
					case 'double': 
					case 'double precision': 
					case 'serial': 
					case 'number': 
						$object_fields[$db->row_data['Field']] = 'number';
						$int_array[] = $db->row_data['Field'];
					break;
					case 'date': 
					case 'year': 
						$object_fields[$db->row_data['Field']] = 'date';
						$date_array[] = $db->row_data['Field'];
					break;
					case 'datetime': 
						$object_fields[$db->row_data['Field']] = 'datetime';
						$datetime_array[] = $db->row_data['Field'];
					break;
						case 'time': 
						$object_fields[$db->row_data['Field']] = 'time';
						$time_array[] = $db->row_data['Field'];
					break;
					case 'timestamp': 
						$object_fields[$db->row_data['Field']] = 'timestamp';
						$int_array[] = $db->row_data['Field']; // these get filtered as integers really
					break;
				}
			}
			
			// get uniques so there aren't duplications for booleans and such
			$int_array = array_unique($int_array);
			$boolean_array = array_unique($boolean_array);
			$string_array = array_unique($string_array); 
			$binary_array = array_unique($binary_array); 
			$date_array = array_unique($date_array); 
			$datetime_array = array_unique($datetime_array); 
			$time_array = array_unique($time_array); 
			
			
			$file_path = Path::toActions().File::osPath('Custom/'.$object_names[$key].'Actions.php');
			
			if(	isset($_POST['list']) && 
				in_array($object_names[$key],array_keys($_POST['list'])) && 
				$_POST['list'][$object_names[$key]] == 1 &&
				!file_exists($file_path)){
				
				// INSERT LOGIC	
				
				$set_columns = array();
				$value_columns = array();
				$data_to_columns = array();
				$integer_columns = array();
				
				foreach($insert_columns as $field){
					$set_columns[] = "{$field}";
					$value_columns[] = ":{$field}";
					if($field == 'created_datetime' || $field == 'created'){
						$data_to_columns[] = "':{$field}' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime')";
					} else if(in_array($field, $date_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getDateTimeObject('{$field}')->getMySQLFormat('date')";
					} else if(in_array($field, $datetime_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getDateTimeObject('{$field}')->getMySQLFormat('datetime')";
					} else if(in_array($field, $time_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getDateTimeObject('{$field}')->getMySQLFormat('time')";
					} else if(in_array($field, $binary_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getBinary('{$field}')";
					} else if(in_array($field, $int_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getInteger('{$field}')";
					} else if(in_array($field, $boolean_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getBoolean('{$field}')";
					} else {
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getString('{$field}')";
					}
					if(in_array($field, $int_array) || $field == $primary_key || in_array($field, $boolean_array)){
						$integer_columns[] = "':{$field}'";
					}
				}
				
				$insert = '\'
			INSERT INTO '.$table_name.' (
				'.implode(",\n\t\t\t\t",$set_columns).'
			) VALUES (
				'.implode(",\n\t\t\t\t",$value_columns).'
			)\',
			// bind data to sql variables
			array(
				'.implode(",\n\t\t\t\t",$data_to_columns).',
				\':'.$primary_key.'\' => $'.$object_names[$key].'->getInteger(\''.$primary_key.'\')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				'.implode(",\n\t\t\t\t",$integer_columns).((count($integer_columns)>0)?',':'').'
				\':'.$primary_key.'\'
			)
		';		
				
				
				// SELECT LOGIC	
				
				$select = '\'
			SELECT 
				'.implode(",\n\t\t\t\t",array_keys($object_fields)).'
			FROM '.$table_name.' 
			WHERE '.$primary_key.'=:'.$primary_key.'\',
			// bind data to sql variables
			array(
				\':'.$primary_key.'\' => (int)$'.$primary_key.'
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				\':'.$primary_key.'\'
			)
		';		
				
				
				// LIST LOGIC	
				
				$list = '\'
			SELECT 
				'.implode(",\n\t\t\t\t",array_keys($object_fields)).'
			FROM '.$table_name.' 
			\',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			\''.$object_names[$key].'\'
		';		
				
				
				// UPDATE LOGIC	
				
				$set_columns = array();
				$data_to_columns = array();
				$integer_columns = array();
				foreach($update_columns as $field){
					$set_columns[] = "{$field}=:{$field}";
					if($field == 'modified_datetime' || $field == 'modified'){
						$data_to_columns[] = "':{$field}' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime')";
					} else if(in_array($field, $date_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getDateTimeObject('{$field}')->getMySQLFormat('date')";
					} else if(in_array($field, $datetime_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getDateTimeObject('{$field}')->getMySQLFormat('datetime')";
					} else if(in_array($field, $time_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getDateTimeObject('{$field}')->getMySQLFormat('time')";
					} else if(in_array($field, $binary_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getBinary('{$field}')";
					} else if(in_array($field, $int_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getInteger('{$field}')";
					} else if(in_array($field, $boolean_array)){
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getBoolean('{$field}')";
					} else {
						$data_to_columns[] = "':{$field}' => \${$object_names[$key]}->getString('{$field}')";
					}
					if(in_array($field, $int_array) || $field == $primary_key){
						$integer_columns[] = "':{$field}'";
					}
				}
				
				$update = '\'
			UPDATE '.$table_name.' 
			SET '.implode(",\n\t\t\t\t",$set_columns).'
			WHERE '.$primary_key.'=:'.$primary_key.'
			\',
			// bind data to sql variables
			array(
				'.implode(",\n\t\t\t\t",$data_to_columns).',
				\':'.$primary_key.'\' => $'.$object_names[$key].'->getInteger(\''.$primary_key.'\')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				'.implode(",\n\t\t\t\t",$integer_columns).((count($integer_columns)>0)?',':'').'
				\':'.$primary_key.'\'
			)
		';
				
				
				// DELETE LOGIC	
				
				$delete = '\'
			DELETE 
			FROM '.$table_name.' 
			WHERE '.$primary_key.'=:'.$primary_key.'\',
			// bind data to sql variables
			array(
				\':'.$primary_key.'\' => (int)$'.$primary_key.'
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				\':'.$primary_key.'\'
			)
		';
			
				// primary key search and replace
				$action = str_replace('$id', '$'.$primary_key, $blank_action);
				$action = str_replace('primary_key', $primary_key, $action);
				// crud methods search and replace
				$action = str_replace('/* insert */', $insert, $action);
				$action = str_replace('/* list */', $list, $action);
				$action = str_replace('/* select */', $select, $action);
				$action = str_replace('/* update */', $update, $action);
				$action = str_replace('/* delete */', $delete, $action);
				
				$action = str_replace('ObjectName', $object_names[$key], $action);
				file_put_contents($file_path, $action);
				chmod($file_path, 0755);
				
				echo '<li>Created '.$file_path.'</li>';
				echo '<li><pre>'.htmlentities($action).'</pre></li>';
			}
			
			if(file_exists($file_path)){
				echo '<li>'.$object_names[$key].' exists</li>';
			} else {
				echo '<li><input type="checkbox" name="list['.$object_names[$key].']" value="1">'.$object_names[$key].'</li>';
			}
		}
		echo '
		</ul>
		<input type="submit" value="create">
		</form>
		';
	}
	
	private function buildForm(){

				$RuntimeInfo = RuntimeInfo::instance();
		$db = $RuntimeInfo->mysql();
				
		if(isset($_POST['o'])){ // handle form submission from xhr request
			
			$page = isset($_POST['page_name'])?$_POST['page_name']:'';
			$full_page_path = isset($_POST['folder_name'])?$_POST['folder_name'].'/'.$page:$page;
			
			$controller = file_get_contents(File::osPath(dirname(__FILE__).'/../../../Framework/Scaffold/Controller.txt'));
			$view = '';
			
			$page_dependencies = '';
			$business_logic = '';
			$page_logic = '';
			$controller_buffer = '';
			
			$objects = array();
			$inputs = array();
			
			foreach($_POST['o'] as $key => $config_option){
				$objects[] = $config_option['form_name'];
				$inputs[$config_option['input_name']] = $config_option['form_name'];
			}
			
			// Get a list of objects used for this page
			$objects = array_unique($objects);
			
//			print_r($objects);
			
			// link objects by name for $page_dependencies
			
			$objects_arr = array();
			foreach($objects as $object_name){
				$objects_arr[] = "'".$object_name."'";
			}
			
			$page_dependencies .= '
		$this->loadModels(array('.implode(',',$objects_arr).'));
		$this->loadActions(array('.implode(',',$objects_arr).'));
		';
			
			// handle business logic
			foreach($objects as $object_name){
				$business_logic .= '
		$this->Input->set(\''.$object_name.'\', new DataObject());
		$this->Errors->set(\''.$object_name.'\', new DataObject());
		$this->Messages->set(\''.$object_name.'\', new DataObject());
		';
			}
			
			$business_logic .= '
			
		if(';
			
			$arr = array();
			foreach($objects as $object_name){
				$arr[] = 'isset($_POST[\''.$object_name.'\'])';
			}
			
			$business_logic .= implode(' && ',$arr).'){
';
			
			foreach($objects as $object_name){
				$business_logic .= '
			$this->Input->set(\''.$object_name.'\', new DataObject($_POST[\''.$object_name.'\']));';
			}
			
			$business_logic .= "\n";
			
			foreach($_POST['o'] as $key => $config_option){
				
				if(isset($config_option['required']) && $config_option['required'] == 'true'){
					switch($config_option['field_type']){
						case 'checkbox':
							
							$business_logic .= '
			if(!in_array($this->Input->get(\''.$config_option['form_name'].'\')->get(\''.$config_option['input_name'].'\'), array_keys( array(\'allowed_keys\') ))) 
			{ $this->Errors->get(\''.$config_option['form_name'].'\')->set(\''.$config_option['input_name'].'\',\'<span style="color:#f00">This is a required field</span>\'); }
';
						
						break;
						case 'date':
							
							
							
						break;
						case 'datetime':
							
							
							
						break;
						case 'file':
							
							
							
						break;
						case 'full_name':
							
							
							
						break;
						case 'password':
							
							$business_logic .= '
			if(!$this->Input->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'isset\') ||
				!$this->Input->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'Is::length\',1,'.$config_option['size'].')){
				$this->Errors->get(\''.$config_option['form_name'].'\')->set(\''.$config_option['input_name'].'\', \'This is a required field.\');
			}
';
						
						break;
						case 'radio_list':
							
							$business_logic .= '
			if(!in_array($this->Input->get(\''.$config_option['form_name'].'\')->get(\''.$config_option['input_name'].'\'), array_keys( array(\'allowed_keys\') ))) 
			{ $this->Errors->get(\''.$config_option['form_name'].'\')->set(\''.$config_option['input_name'].'\',\'<span style="color:#f00">This is a required field</span>\'); }
';
							
						break;
						case 'select':
							
							$business_logic .= '
			if(!in_array($this->Input->get(\''.$config_option['form_name'].'\')->get(\''.$config_option['input_name'].'\'), array_keys( array(\'allowed_keys\') ))) 
			{ $this->Errors->get(\''.$config_option['form_name'].'\')->set(\''.$config_option['input_name'].'\',\'<span style="color:#f00">This is a required field</span>\'); }
';
							
						break;
						case 'text':
							
							$business_logic .= '
			if(!$this->Input->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'isset\') ||
				!$this->Input->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'Is::length\',1,'.$config_option['size'].')){
				$this->Errors->get(\''.$config_option['form_name'].'\')->set(\''.$config_option['input_name'].'\', \'This is a required field.\');
			}
';
							
						break;
						case 'textarea':
							
							$business_logic .= '
			if(!$this->Input->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'isset\') ||
				!$this->Input->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'Is::length\',1,65000)){
				$this->Errors->get(\''.$config_option['form_name'].'\')->set(\''.$config_option['input_name'].'\', \'There was an error with this field.\');
			}
';
							
						break;
						case 'time':
							
							
							
						break;
					}
				}
			}
			
			$business_logic .= '
		}
		
		// run actions if no errors are present
		if(';
			
			$arr = array();
			foreach($objects as $object_name){
				$arr[] = '$this->Errors->get(\''.$object_name.'\')->length()<1';
			}
			
			$business_logic .= implode(' && ',$arr).'){';
			$arr = array();
			foreach($objects as $object_name){
				$business_logic .= '
			$'.$object_name.' = new '.$object_name.'($this->Input->get(\''.$object_name.'\')->toArray());
			// '.$object_name.'Actions::insert'.$object_name.'($'.$object_name.');';
			}
			
			$business_logic .= '
			$this->_data[\'saved\']=true;
		}
		
		// redirect after post if successful (do not redirect ajax requests)
		if(isset($this->_data[\'saved\'])){
			$this->redirect(\'/'.$full_page_path.'?success=1\');
		} else if(isset($_GET[\'success\'])){
			if($_GET[\'success\'] == \'1\'){
				$this->_data[\'saved\'] = true;
			} else if($_GET[\'success\'] == \'2\') {
				$this->_data[\'deleted\'] = true;
			}
		}
';

			foreach($objects as $object_name){
				$business_logic .= '
		$this->_data[\''.$object_name.'\'] = $'.$object_name.'; // '.$object_name.'Actions::select'.$object_name.'List();';
			}
			
			
			// build page options
			
			$page_logic .= '// Shorthand object references';
			foreach($objects as $object_name){
				$page_logic .= '
		$'.$object_name.' = $this->_data[\''.$object_name.'\'];';
			}
			
			$page_logic .= '
		
		// Messages for display after redirects
		$form_message = \'\';
		if(isset($this->_data[\'saved\'])){
			$form_message = \'Form submitted successfully.\';
		} else if(isset($this->_data[\'deleted\'])){
			$form_message = \'Form update submitted successfully.\';
		}
';
		
			foreach($_POST['o'] as $key => $config_option){
				$page_logic .= '
		$'.$config_option['input_name'].'_hint = ($this->Errors->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'empty\'))?\''.$config_option['hint'].'\':\'<em class="error">\'.$this->Errors->get(\''.$config_option['form_name'].'\')->get(\''.$config_option['input_name'].'\').\'</em>\';';
			}

			$arr = array();
			foreach($inputs as $config_option => $object_name){
				$arr[] = '\''.$config_option.'_value\' => $'.$object_name.'->getData(\''.$config_option.'\',\'Parse::body\')';
				$arr[] = '\''.$config_option.'_hint\' = '.$config_option.'_hint';
			}
			
			$fields_for_print = implode(',
			',$arr).'
			\'BR\'=>BR';
			
			// print out options
			if(!isset($_POST['folder_name'])){
				$controller = str_replace('/../../../../../source/Applications/\'.$RuntimeInfo->getApplicationName()','/..',$controller);
			} else {
				$controller = str_replace('/../../../../../source/Applications/\'.$RuntimeInfo->getApplicationName()','/../..',$controller);
			}
			$controller = str_replace('PageName', $page, $controller);
			$controller = str_replace('FolderPagePath', $full_page_path, $controller);
			$controller = str_replace('/* page dependencies */', $page_dependencies, $controller);
			$controller = str_replace('/* business logic */', $business_logic, $controller);
			$controller = str_replace('/* page logic */', $page_logic, $controller);
			$controller = str_replace('\'BR\'=>BR', $fields_for_print, $controller);
			
			$view .= '
<fieldset>
	<legend>'.$page.'</legend>
	<form action="?" method="POST">
		<ul>
';
			
			$li_template = file_get_contents(File::osPath(dirname(__FILE__).'/../Views/fragments/form/line.htm'));
			foreach($_POST['o'] as $key => $config_option){
				$option_template = file_get_contents(File::osPath(dirname(__FILE__).'/../Views/fragments/form/fields/'.$config_option['field_type'].'.htm'));
//				die($option_template);
				$li = str_replace('{% detail %}',$config_option['detail'],$li_template);
				$li = str_replace('{% input_type %}',$config_option['field_type'],$li);
				$li = str_replace('{% li_width %}','',$li);
				$option = str_replace('{% input_value %}','{% '.$config_option['input_name'].'_value %}',$option_template);
				$option = str_replace('{% hint %}','{% '.$config_option['input_name'].'_hint %}',$option);
				$option = str_replace('{% size %}',isset($config_option['size'])?$config_option['size']:'',$option);
				$option = str_replace('{% required %}',isset($config_option['required'])?'required="required"':'',$option);
				$option = str_replace('{% tab_index %}',($key+1),$option);
				$option = str_replace('{% span_width %}','full',$option);
				$li = str_replace('{% inputs %}',$option,$li);
				$li = str_replace('{% form_name %}',$config_option['form_name'],$li);
				$li = str_replace('{% input_name %}',$config_option['input_name'],$li);
				
				$view .= $li;
			}
			
			$view .= '
			
			<li>
				<div>
					<input name="save" class="submit" type="submit" value="Save">
				</div>
			</li>
		</ul>
	</form>
</fieldset>
			';
			
			echo '
<h3>Controller Code:</h3>
<div id="controller_source"><pre>
'.(htmlentities($controller)).'
</pre></div>
<h3>View Code:</h3>
<div id="view_source"><pre>
'.(htmlentities($view)).'
</pre></div>
			';
			
			die;
			
		} else { // handle page request 
		
			$FormParser = new TemplateParser();
			$form_types = File::filesInFolderToArray(File::osPath(dirname(__FILE__).'/../Views/fragments/form/fields'));
			$this->forms = array();
			$this->dummy_forms = array();
			$this->field_vars = array();
			foreach($form_types as $path => $file_name){
				$class_name = substr($file_name, 0, -4);
				$FormParser->load('fragments/form/fields/'.$file_name);
				if(count($FormParser->getVars())>0){
					$this->forms[$class_name] = file_get_contents($path);
	//				$forms[$file_name.'_inputs'] = $FormParser->getVars(true);
					$this->field_vars[$class_name] = $FormParser->getVars(true);
					$replacement_array = array();
					foreach($this->field_vars[$class_name] as $var_name){
						$replacement_array[$var_name] = $var_name;
					}
					$this->dummy_forms[$class_name] = $FormParser->parse($replacement_array);
				}
			}
		}
		
		// RENDER HTML FROM HERE ON (this was a second method)
		
		$this->addCss('scaffold');
		$this->addJs('scaffold');
		
		$form='';
		$this->_tpl->load('fragments/form/line.htm');
		$li = $this->_tpl->getTemplate('fragments/form/line.htm');
		$li_vars = $this->_tpl->getVars(true);
		foreach($this->dummy_forms as $key => $dummy_form){
			$form .= $this->_tpl->parse(array(
				'input_type' => $key, 
				'li_width' => '', 
				'input_name' => $key, 
				'form_name' => 'input_type_selector', 
				'detail'=>$key,
				'inputs'=>$dummy_form.'
<script id="tpl_'.$key.'" type="text/x-jquery-tmpl">
'.$this->forms[$key].'
</script>
				'
			));
		}
		
		$this->_tpl->load('fragments/form/form.htm');
		$this->_page_body = '
<script id="tpl_li" type="text/x-jquery-tmpl">
'.$li.'
</script>
<script type="text/javascript">
var form_vars = '.json_encode($this->field_vars).';
var li_vars = '.json_encode($li_vars).';
</script>

<fieldset>
	<legend>Sample Form</legend>
	<form id="page_properties">
		<div>
			<table><tr>
					<td><strong>Path:</strong> </td>
					<td><input name="folder_name" type="text" value=""> / 
					<input name="page_name" type="text" value="PageName"></td>
					<td><small>... ex: http://example.com/FolderName/PageName or http://example.com/PageName</small></td>
			</tr></table>
		</div>
	</form>
</fieldset>
<br>
<div id="form_field_factory">
	<div class="right">
		<form id="sample_field_form" action="?">
			<div>
				<h3 style="margin-top:5px">Sample Field:</h3>
			</div>
			<ul></ul>
			<ul id="sample_field">
				<!-- this is where the sample field goes -->
			</ul>
		</form>
		<form id="form_variables_form" action="?">
			<h3>Variables for field:</h3>
			<div id="form_variables">
				<!-- this is where the forms variables go for a single input type -->
			</div>
			<input type="submit" value="Add field to form" style="float:right;margin-top:20px;margin-bottom:5px">
		</form>
	</div>
	<div class="left">
		<h3 style="margin-top:0px;margin-bottom:5px">Pick a form field to get started:</h3>
		<select id="field_type_selector" size="7">
			<!-- this is where the input types you can choose from go -->
		</select>
	</div>
	<div style="clear:both; height:1px;">&nbsp;</div>
</div>

<form id="input_type_selector" name="input_type_selector" class="wufoo page1" action="?" method="POST" style="clear:both;">
	<fieldset>
		<legend>Sample Form</legend>
		<div>This is roughly what your form will look like once code has been generated. 
			If you are satisfied with these options, click the build form button to get the code.</div>
		<div>
			<ul id="sample_form_layout">
				<!-- this is where the sample form goes -->
			</ul>
			
			<input id="submit_button" type="submit" value="Build form code" style="margin-top:20px;margin-bottom:5px">
		</div>
	</fieldset>
</form>
<div style="display:none"><form id="input_type_selector"><ul>'.$form.'</ul></form></div>
<div id="results_window"></div>';
	}
	
}
