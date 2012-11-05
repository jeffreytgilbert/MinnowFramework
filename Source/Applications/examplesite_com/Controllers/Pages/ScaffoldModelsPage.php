<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ScaffoldModelsPage extends TemplatedPageRequest{
	
	protected function loadIncludedFiles(){
	}
	
	protected function handleRequest(){
		$RuntimeInfo = RuntimeInfo::instance();
		$db = $RuntimeInfo->mysql();
				
		$blank_model = file_get_contents(os_path(dirname(__FILE__).'/../../../Framework/Scaffold/Model.txt'));
		
		$table_names = array();
		$object_names = array();
		$db->query('SHOW TABLES', __LINE__, __FILE__);
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
			$db->query('DESC '.$table_name, __LINE__, __FILE__);
			while($db->readRow()){
				$split_pos = stripos($db->row_data['Type'], '(');
				if($split_pos > 0){
					$type = substr($db->row_data['Type'],0,$split_pos);
				} else {
					$type = $db->row_data['Type'];
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
					case 'datetime': 
					case 'time': 
					case 'year': 
						$object_fields[$db->row_data['Field']] = 'DataType::DATE';
					break;
					case 'timestamp': 
						$object_fields[$db->row_data['Field']] = 'DataType::TIMESTAMP';
					break;
				}
			}
			
			$file_path = os_path(dirname(__FILE__).'/../Models/'.$object_names[$key].'.php');
			
			if(	isset($_POST['list']) && 
				in_array($object_names[$key],array_keys($_POST['list'])) && 
				$_POST['list'][$object_names[$key]] == 1 &&
				!file_exists($file_path)){
				$buffer = array();
				foreach($object_fields as $field => $type){
					$buffer[] = "'{$field}'=>{$type}";
				}
				
				$model = str_replace('ObjectName', $object_names[$key], $blank_model);
				$model = str_replace('(array());', '(array(
			'.implode(",\n\t\t\t",$buffer).'
		),true);', $model);
				file_put_contents($file_path, $model);
				chmod($file_path, 0755);
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
	
	public function renderPage(){
		die();
	}
}

