<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ScaffoldActionsPage extends PageController implements HTMLCapable{
	
	protected function loadIncludedFiles(){
	}
	
	protected function handleRequest(){
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
						$data_to_columns[] = "':{$field}' => RIGHT_NOW_GMT";
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
			// which fields are integers
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
			// which fields are integers
			array(
				\':'.$primary_key.'\'
			),
			// return as this object collection type
			\''.$object_names[$key].'\'
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
			// which fields are integers
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
						$data_to_columns[] = "':{$field}' => RIGHT_NOW_GMT";
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
			// which fields are integers
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
			// which fields are integers
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
	
	public function renderHTML(){
		die();
	}
}

