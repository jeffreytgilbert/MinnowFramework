<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class FormBuilderPage extends PageController implements HTMLCapable, JSONCapable{

	public 
		$forms,
		$dummy_forms,
		$data;
	
	public function getTableDefinition($table_name){
		try{
			$Validator = new ValidString($table_name);
			$Validator->allowSimpleAlphabetCharactersOnly(false,true);
		} catch(Exception $e) {
			$this->flashError($e->getCode(), 'Invalid table format requested.');
		}
		
		$db = $this->getConnections()->MySQL();
		$db->query('DESCRIBE `'.$table_name.'`');
// 		$x=0;
		$table_definition = array();
		while($db->readRow()){
			//$table_definition[] = $db->row_data;
			
			$d = $db->row_data; // d for details
			$f = $d['Field'];
			$table_definition[$f] = array();
			
			if(mb_strstr($d['Type'],'(') != ''){ // something with a size attached. Get the size from it and trip the type down
				$type_chunks = explode('(', $d['Type'], 2);
				
				// define the data type
				$table_definition[$f]['type'] = $type_chunks[0];
				
				$type_chunks2 = explode(')', $type_chunks[1]);
				if(count($type_chunks2)>1 && trim($type_chunks2[1]) != ''){ // secondary detail, like unsigned
					$table_definition[$f]['size'] = $type_chunks2[0];
					$table_definition[$f]['type_detail'] = trim($type_chunks2[1]);
				} else {
					$table_definition[$f]['size'] = $type_chunks2[0];
				}
			} else {
				$table_definition[$f]['type'] = $d['Type'];
			}
			
			if(isset($table_definition[$f]['size'])){
				if(mb_substr($table_definition[$f]['type'],-3) == 'int'){
					if(isset($table_definition[$f]['type_detail']) && $table_definition[$f]['type_detail'] == 'unsigned'){
						$table_definition[$f]['min'] = 0;
						switch($table_definition[$f]['type']){
							case 'tinyint':
								$table_definition[$f]['max'] = 255;
								break;
							case 'smallint':
								$table_definition[$f]['max'] = 65535;
								break;
							case 'mediumint':
								$table_definition[$f]['max'] = 16777215;
								break;
							case 'int':
								$table_definition[$f]['max'] = 4294967295;
								break;
							case 'largeint':
								$table_definition[$f]['max'] = 18446744073709551615;
								break;
						}
					} else {
						switch($table_definition[$f]['type']){
							case 'tinyint':
								$table_definition[$f]['min'] = -127;
								$table_definition[$f]['max'] = 127;
								break;
							case 'smallint':
								$table_definition[$f]['min'] = -32767;
								$table_definition[$f]['max'] = 32767;
								break;
							case 'mediumint':
								$table_definition[$f]['min'] = -8388607;
								$table_definition[$f]['max'] = 8388607;
								break;
							case 'int':
								$table_definition[$f]['min'] = -2147483647;
								$table_definition[$f]['max'] = 2147483647;
								break;
							case 'largeint':
								$table_definition[$f]['min'] = -9223372036854775807;
								$table_definition[$f]['max'] = 9223372036854775807;
								break;
						}
					}
				}
			}
			
			if(isset($d['Null']) && $d['Null'] == 'NO'){
				$table_definition[$f]['required'] = true;
			} else {
				$table_definition[$f]['required'] = false;
			}
			
			$table_definition[$f]['default'] = (isset($d['Default']) && !empty($d['Default']))?$d['Default']:null;
			
			$table_definition[$f]['primary'] = (isset($d['Key']) && $d['Key'] == 'PRI')?true:false;
			
			$table_definition[$f]['primary'] = (isset($d['Key']) && $d['Key'] == 'PRI')?true:false;

// 					case 'Extra': // autoinc, etc
			
		}
		
		return $table_definition;
		
	}
	
	// got table definition
	// need form fields in mustache templates
	// write js to build the form
	// write sitemap grabber for page list for save paths
	// 
	
	public function handleRequest(){
		
		$TableDefinitionForm = $this->getForm('TableDefinition',FormValidator::METHOD_GET);
		if($TableDefinitionForm->isSubmitted()){
			$table_definition = $this->getTableDefinition($TableDefinitionForm->getFieldData('table_name'));
			echo json_encode($table_definition); die;
			//$this->getDataObject()->set($TableDefinitionForm->getFieldData('table_name'), $table_definition);
			return;
		}
		
// 		$TableDefinitionForm = $this->getForm('TableDefinition',FormValidator::METHOD_GET);
// 		if($TableDefinitionForm->isSubmitted()){
// 			$this->handleRequestForTableDefinition($TableDefinitionForm->getFieldData('table_name'));
// 			return;
// 		}
		
		$db = $this->getConnections()->MySQL();
		
		// ----- Models & Actions -----
		// Gather requirements:
		// A list of tables
		
		$table_names = array();
		$object_names = array();
		$db->query('SHOW TABLES');
		$x=0;
		while($db->readRow()){
			$table_name = array_pop($db->row_data);
			$table_names[$table_name] = $table_name;
			$object_name_parts = explode('_',$table_name);
			foreach($object_name_parts as $key => $object_name_part){
				$object_name_parts[$key] = ucfirst($object_name_part);
			}
			$object_names[$table_name] = implode('',$object_name_parts);
			$x++;
		}
//  		pr($table_names);
//  		pr($object_names);
		$this->getDataObject()->set('table_names', $table_names);
		$this->getDataObject()->set('object_names', $object_names);
		
		// A list of tables that dont have actions
		$missing_actions = array();
		foreach($object_names as $key => $object_name){
			if(!File::exists(Path::toActions().'Custom/'.$object_name.'Actions.php')){
				$missing_actions[$key] = $object_name;
			}
		}
// 		pr($missing_actions);
		$this->getDataObject()->set('missing_actions', $missing_actions);
		
		// A list of tables that dont have models
		$missing_models = array();
		foreach($object_names as $key => $object_name){
			if(!File::exists(Path::toModels().'Custom/'.$object_name.'.php')){
				$missing_models[$key] = $object_name;
			}
		}
// 		pr($missing_models);
		$this->getDataObject()->set('missing_models', $missing_models);
		
		// ----- Form / View builder -----
		// A list of form input types
		$allowed_input_types = ['checkbox','date','datetime','file','full_name','mutliselect','password','radio_list','select','text','textarea','time'];
//		pr($allowed_input_types);
		$this->getDataObject()->set('allowed_input_types', $allowed_input_types);
		
		// Final json encodable array
		$validators = array();
		
		// A list of validator types
		$validator_types = array();
		$validator_type_methods = array();
		$validator_type_method_arguments = array();
		$methods = get_class_methods('FormValidator');
		foreach($methods as $method){
			if(mb_substr($method, 0, mb_strlen('check')) == 'check' && $method != 'checkArray'){
				$validator_type = mb_substr($method, mb_strlen('check'), mb_strlen($method) - mb_strlen('check'));
				if(class_exists('Valid'.$validator_type)){
					$validator_types[] = $validator_type;
					//$tmp_validator_type_methods = get_class_methods('Valid'.$validator_type);
					$tmp_validator_type_methods = array();
					$ReflechtionClass = new ReflectionClass('Valid'.$validator_type);
					if(count($ReflechtionClass->getMethods()) > 0){
						$these_methods = $ReflechtionClass->getMethods();
						
						foreach($these_methods as $method){
							$tmp_validator_type_methods[$method->name] = $method->class;
						}
					}
					
					foreach($tmp_validator_type_methods as $the_method => $class_origin){
						if(!in($the_method, array('__construct','cast','getData','getErrors', 'throwException'))){
							//$validator_type_methods['Valid'.$validator_type][] = $the_method;
							$validator_type_methods['Valid'.$validator_type][] = array($class_origin=>$the_method);
							
							$Reflection = new ReflectionMethod($class_origin, $the_method);
							if(count($Reflection->getParameters()) > 0){
								$params = $Reflection->getParameters();
								$actual_params = array();
								foreach($params as $param){
									
									$param_info = ReflectionParameter::export(
										array(
											$param->getDeclaringClass()->name, 
											$param->getDeclaringFunction()->name
										), 
										$param->name, 
										true
									);
									$param_info = str_replace(array('<','>'), array('(',')'), $param_info);
									$start = mb_strpos($param_info,'[')+1;
									$end = (mb_strlen($param_info) - $start)-1;
									$param_definition = trim(mb_substr($param_info, $start, $end));
									
									$actual_params[$param->name] = $param_definition;
								}
								$validator_type_method_arguments['Valid'.$validator_type][$the_method] = $actual_params;
							}
						}
					}
				}
			}
		}
		
		foreach($validator_type_methods as $validator_name => $validator_methods){
//			pr($validator_methods);die;
			
			$methods = array();
			foreach($validator_methods as $validator_method_name){
// 				pr(__LINE__);
// 				pr($validator_name);
// 				pr($validator_method_name);
				// Set the class origin 
				$validator_class_origin = $validator_name;
				if(is_array($validator_method_name)){
					$validator_class_origin = key($validator_method_name);
					$validator_method_name = current($validator_method_name);
				}
//				pr('ME'.$validator_method_name);
				
				if(isset($validator_type_method_arguments[$validator_name][$validator_method_name]) 
				&& count($validator_type_method_arguments[$validator_name][$validator_method_name]) > 0){
					$params = $validator_type_method_arguments[$validator_name][$validator_method_name];
// 					pr('MEH'.$validator_method_name);
// 					pr($params);
					$validator_method_parameters = array();
					foreach($params as $param_name => $param_definition){
// 						pr($param_name);
// 						pr($param_definition);
						$validator_method_parameters[] = array(
							'parameter_name'=>$param_name,
							'parameter_definition'=>$param_definition
						);
					}
				} else {
					$validator_method_parameters = array();
				}
				
				$methods[] = array(
					'method_name'=>$validator_method_name,
					'class_origin'=>$validator_class_origin,
					'parameters'=>$validator_method_parameters
				);
			}
			
			$validators[] = array(
				'validator_type'=>substr($validator_name,5,mb_strlen($validator_name)),
				'methods'=>$methods
			);
		}
		
// 		pr("
// \$validators = {
// 	'validators':[
// 		{ 
// 			'validator_type': 'blah',
// 			'methods': [
// 				{
// 					'method_name': 'blah',
// 					'parameters': [
// 						{
// 							'parameter_name':'blah',
// 							'parameter_definition':'blah'
// 						}
// 					]
// 				}
// 			]
// 		}
// 	]
// };
// 				");
		
// 		pr($validators);
		
/*

		$validators = {
			'validators':[
				{ 
					'validator_type': 'blah',
					'methods': [
						{
							'method_name': 'blah',
							'parameters': [
								{
									'parameter_name':'blah',
									'parameter_definition':'blah'
								}
							]
						}
					]
				}
			]
		};
		
{{#validators}}
	<span>{{ validator_type }}</span>
	{{#methods}}
		<label class="checkbox inline">
		  <input type="checkbox" id="" value="{{method_name}}"> {{method_name}}
		</label>
		{{#parameters}}
			<input class="span1" type="text" placeholder="{{parameter_name}}">
		{{/parameters}}
	{{/methods}}
{{/validators}}		
*/	
		
//		pr($validator_type_methods);
//		pr($validator_type_method_arguments);
		//		pr($validator_types);
		$this->getDataObject()->set('validators', $validators);
		
		// Requery the database for the ones that exist
		$SitemapCollection = SitemapActions::selectList();
		
		// Put them in a data field so they can be used on the view
		$this->getDataObject()->set('SitemapCollection',$SitemapCollection);
		
		
		$FormBuilderForm = $this->getForm('FormBuilder');
		
		// Check for form login from local page request
		// If data exists in expected form, check it as a login against the db
		if($FormBuilderForm->hasBeenSubmitted()){
			
			// Handle errors one at a time (if you want to handle them all at the same time, throw each one in a try catch block of its own
			$errors = array();
			
			try{
				$FormBuilderForm->checkString('path')->required()->validate();
				$FormBuilderForm->checkWords('description')->required()->allowUTF8WordsAndNumbersAndPunctuation(false)->maxLength(160);
				
			} catch(Exception $e){
				$errors = $FormBuilderForm->getCurrentErrors();
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
	public function renderHTML(){
		$this->addCss('Libraries/Bootstrap/colorpicker');
		$this->addCss('Libraries/Bootstrap/datetimepicker');
		return $this->_page_body = parent::renderHTML();
	}
// 	public function renderHTML(){
// 		$template = File::read(Path::toJS().'Pages/DeveloperTools/FormBuilder/layout.mustache');
// 		return $this->_page_body = $this->getHelpers()->Mustache()->render($template, $this->data);
// 	}
	

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
