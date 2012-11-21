<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ScaffoldModelsPage extends PageController implements HTMLCapable{
	
	protected function loadIncludedFiles(){
	}
	
	protected function handleRequest(){
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
	
	public function renderHTML(){
		die();
	}
}

