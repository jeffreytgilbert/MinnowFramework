<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ScaffoldFormsPage extends PageController implements HTMLCapable{
	
	public $forms;
	public $dummy_forms;
	
	protected function loadIncludedFiles(){
	}
	
	public function handleRequest(){
		$RuntimeInfo = RuntimeInfo::instance();
		$db = $RuntimeInfo->mysql();
				
		if(isset($_POST['o'])){ // handle form submission from xhr request
			
			$page = isset($_POST['page_name'])?$_POST['page_name']:'';
			$full_page_path = Is::set($_POST['folder_name'])?$_POST['folder_name'].'/'.$page:$page;
			
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
			if(!$this->Input->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'Is::set\') ||
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
			if(!$this->Input->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'Is::set\') ||
				!$this->Input->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'Is::length\',1,'.$config_option['size'].')){
				$this->Errors->get(\''.$config_option['form_name'].'\')->set(\''.$config_option['input_name'].'\', \'This is a required field.\');
			}
';
							
						break;
						case 'textarea':
							
							$business_logic .= '
			if(!$this->Input->get(\''.$config_option['form_name'].'\')->getData(\''.$config_option['input_name'].'\',\'Is::set\') ||
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
			if(!Is::set($_POST['folder_name'])){
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
				$option = str_replace('{% required %}',Is::set($config_option['required'])?'required="required"':'',$option);
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
		
	}
	
	public function renderHTML(){
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

