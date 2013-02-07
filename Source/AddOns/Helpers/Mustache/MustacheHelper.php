<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// this one needs to have the sessions config stored in the config (which should extend model) and then call its settings from within if using db as the config

final class MustacheHelper extends Helper{
	
	public function __construct(Model $Config){
		// run parent construct action to save settings
		parent::__construct($Config);
		
		Run::fromHelpers('Mustache/Requirements/src/Mustache/Autoloader.php');
		Mustache_Autoloader::register();
// 		Run::fromHelpers('Mustache/Requirements/src/Mustache/Engine.php');
		
		$this->_instance = new Mustache_Engine(
			array(
				'cache' => File::osPath(RuntimeInfo::instance()->appSettings()->get('temporary_files_folder').'/MustacheTemplates'),
				'cache_file_mode' => 0755, // May not be necessary
				'logger' => RuntimeInfo::instance()->getConnections()->Logger()->getLoggerInstance() // Use default monolog instance for logging
			)
			/* options from the docs here: https://github.com/bobthecow/mustache.php/wiki
			array(
				'template_class_prefix' => '__MyTemplates_',
				'cache' => dirname(__FILE__).'/tmp/cache/mustache',
				'cache_file_mode' => 0666, // Please, configure your umask instead of doing this :)
				'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views'),
				'partials_loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views/partials'),
				'helpers' => array('i18n' => function($text) {
					// do something translatey here...
				}),
				'escape' => function($value) {
					return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
				},
				'charset' => 'ISO-8859-1',
				'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
			)
			*/
		);
	}
	
	public function getInstance(){
		if($this->_instance instanceof Mustache_Engine) return $this->_instance;
		return new Mustache_Engine();
	}
	
	public function __destruct(){
		// unset($this);
	}
}
