<?php

class TemplateParser{
	private $_templates_dir = '';
	private $_templates = array();
	private $_current_template = '';
	
	public function __construct($application_folder_name=null){
		if(!is_null($application_folder_name)){
			$this->_templates_dir = File::osPath(dirname(__FILE__).'/../../Applications/'.$application_folder_name.'/Views/');
		} else {
			$RuntimeInfo = RuntimeInfo::instance();
			$this->_templates_dir = File::osPath(dirname(__FILE__).'/../../Applications/'.$RuntimeInfo->getApplicationName().'/Views/');
		}
	}

	public static function cast(TemplateParser &$TemplateParser){ return $TemplateParser; }
	
	public function getArrayOfTemplatesWithIdsAsKeys(){
		$list_of_templates=array();		
		foreach($this->_templates as $path => $template){
			$list_of_templates[md5($path).basename($path)] = $template;
		}
		return $list_of_templates;
	}
	
	public function getArrayOfTemplatesWithFileNamesAsKeys(){
		$list_of_templates=array();		
		foreach($this->_templates as $path => $template){
			$list_of_templates[basename($path)] = $template;
		}
		return $list_of_templates;
	}
	
	public function getArrayOfTemplatesWithFilePathsAsKeys(){
		return $this->_templates;
	}
	
	public function load($template_path){
//		echo $template_path;
		if(!isset($this->_templates[$template_path])){
			$this->_templates[$template_path] = file_get_contents(File::osPath($this->_templates_dir.$template_path));
		}
		$this->_current_template = $template_path;
	}
	
	private static function cbFunc($match,$hashed_array){
		return isset($hashed_array[$match])?$hashed_array[$match]:'';
	}
	
	public function parse(Array $hashed_array = array()){
//		pr($hashed_array);
		if(count($hashed_array)===0){ return $this->_templates[$this->_current_template]; }
		if($this->_current_template === '') { return ''; }
		
		//$pattern = '/\{%[\s]*?([\w-_]*?)[\s]*?%\}/'; // {% var-name %}
		//$subject = '<div>{% name %} # {% visits %} @ {% last_visited %}</div>';
		
		//$pattern = '/\{\{[\s]*?([\w-_]*?)[\s]*?\}\}/'; // {{ var-name }}
		//$subject = '<div>{{ name }} # {{ visits }} @ {{ last_visited }}</div>';
		
		$pattern = '/\{%[=]*?[\s]*?([\w-_]*?)[\s]*?%\}/'; // {% var-name %} or {%= var-name %}
//		$subject = '<div>{%= name %} # {% visits %} @ {% last_visited %}</div>';
		$matches = array();
		
		return "\n".preg_replace($pattern.'e', 
			"TemplateParser::cbFunc('\\1',\$hashed_array)",
			$this->_templates[$this->_current_template]);
//		return preg_replace($pattern,'\1',$this->_templates[$this->_current_template]); // preg_replace ($pattern, $replacement, $subject, $limit = null, &$count = null)
	}
	
	public function getVars($return_uniques = false){
		if($this->_current_template === '') { return ''; }
		
		$pattern = '/\{%[=]*?[\s]*?([\w-_]*?)[\s]*?%\}/'; // {% var-name %} or {%= var-name %}
		$matches = array();
		
		preg_match_all($pattern,$this->_templates[$this->_current_template],$matches);
		return $return_uniques?array_unique($matches[1]):$matches[1];
	}
	
	public function printFormatedVarsArray(){
		$vars = $this->getVars(true);
		if(count($vars) == 0){ return false; }
		$tmp = array();
		foreach($vars as $placeholder){
			$tmp[] = "'{$placeholder}' => \${$placeholder}";
		}
		echo implode(",\n<br>",$tmp).'<br><br>';
		return true;
	}
	
	public function setTemplate($template,$template_name = 'InlineTemplate'){
		$this->_current_template = $template_name;
		$this->_templates[$template_name] = $template;
	}
	
	public function getTemplate($template_path){
		if(!isset($this->_templates[$template_path])){
			$this->_templates[$template_path] = file_get_contents(File::osPath($this->_templates_dir.$template_path));
		}
		return $this->_templates[$template_path];
	}
	
	public function render(Array $hashed_array){
		echo $this->parse($hashed_array);
	}
	
	/**
	 * @deprecated
	 * @param string $path
	 */
	public function runCodeReturnOutput($path){
		throw('Deprecated method call. Call this method from the Controller, not TemplateParser class.');
	}
}
