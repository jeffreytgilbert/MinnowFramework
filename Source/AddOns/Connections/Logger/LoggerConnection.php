<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// this one needs to have the sessions config stored in the config (which should extend model) and then call its settings from within if using db as the config

// [default]
// debug_level = 100;

// log_to_table = false;
// mysql_connection_name = "default"; This is the name of the connection in your mysql connection settings ini for Minnow
// mysql_table_name = ""; This is the table, but it can be in another database if the mysql user has permission. 
// 					 ; Ex: mysql_table_name = "`database2`.`log_here`"

// log_to_browsers = false; FirePHP plugin for Firefox and ChromePHP for Chrome

// log_to_file = false;
// log_file_name = "Access"; Minnow will attempt to create
// log_file_path = ""; If empty, the path defaults to Temp/Logs

// with_php_info = false; Logs line numbers, file names, etc
// with_web_info = false; Logs the web request uri and web info
// with_memory_info = false; Logs the memory usage of the script

final class LoggerConnection extends Connection{
	
	private 
		$_debug_level,
		$_LoggerInstance;
	
	public function __construct(Model $Config, $connection_name='default'){
		// run parent construct action to save settings
		parent::__construct($Config, $connection_name);
		
		// Manually load all these PSR requirements
		Run::fromConnections('Logger/Requirements/src/Psr/Log/InvalidArgumentException.php');
		Run::fromConnections('Logger/Requirements/src/Psr/Log/LoggerAwareInterface.php');
		Run::fromConnections('Logger/Requirements/src/Psr/Log/LoggerAwareTrait.php');
		Run::fromConnections('Logger/Requirements/src/Psr/Log/LoggerInterface.php');
		Run::fromConnections('Logger/Requirements/src/Psr/Log/AbstractLogger.php');
		Run::fromConnections('Logger/Requirements/src/Psr/Log/LoggerTrait.php');
		Run::fromConnections('Logger/Requirements/src/Psr/Log/LogLevel.php');
		Run::fromConnections('Logger/Requirements/src/Psr/Log/NullLogger.php');
		
		// Manually load all these Logger requirements
		Run::fromConnections('Logger/Requirements/src/Monolog/Logger.php');
		
		Run::fromConnections('Logger/Requirements/src/Monolog/Formatter/FormatterInterface.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Formatter/ChromePHPFormatter.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Formatter/JsonFormatter.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Formatter/NormalizerFormatter.php');
 		Run::fromConnections('Logger/Requirements/src/Monolog/Formatter/LineFormatter.php');
// 		Run::fromConnections('Logger/Requirements/src/Monolog/Formatter/LongstashFormatter.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Formatter/WildfireFormatter.php');
// 		Run::fromConnections('Logger/Requirements/src/Monolog/Formatter/GelfFormatter.php');
		
 		Run::fromConnections('Logger/Requirements/src/Monolog/Handler/HandlerInterface.php');
 		Run::fromConnections('Logger/Requirements/src/Monolog/Handler/AbstractHandler.php');
 		Run::fromConnections('Logger/Requirements/src/Monolog/Handler/AbstractProcessingHandler.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Handler/MinnowDatabaseHandler.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Handler/ChromePHPHandler.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Handler/FirePHPHandler.php');
 		Run::fromConnections('Logger/Requirements/src/Monolog/Handler/StreamHandler.php');
// 		Run::fromConnections('Logger/Requirements/src/Monolog/Handler/NullHandler.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Handler/RotatingFileHandler.php');
		
		Run::fromConnections('Logger/Requirements/src/Monolog/Processor/IntrospectionProcessor.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Processor/MemoryProcessor.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Processor/MemoryPeakUsageProcessor.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Processor/MemoryUsageProcessor.php');
// 		Run::fromConnections('Logger/Requirements/src/Monolog/Processor/PsrLogMessageProcessor.php');
		Run::fromConnections('Logger/Requirements/src/Monolog/Processor/WebProcessor.php');
		
		$levels = array(
			100 => 'DEBUG',
			200 => 'INFO',
			250 => 'NOTICE',
			300 => 'WARNING',
			400 => 'ERROR',
			500 => 'CRITICAL',
			550 => 'ALERT',
			600 => 'EMERGENCY'
		);
		
		$debug_levels = array_flip($levels);
		
		if(in(upper($this->_Config->get('debug_level')),$levels)){
			$this->_debug_level = $debug_levels[upper($this->_Config->get('debug_level'))];
		}
		
		$this->_LoggerInstance = new Monolog\Logger($connection_name);
		
		if($this->_Config->getBoolean('log_to_table')){
			$this->_LoggerInstance->pushHandler(new Monolog\Handler\MinnowDatabaseHandler(
				$this->_Config->get('mysql_connection_name'), 
				$this->_Config->get('mysql_table_name')
			));
		}
		
		if($this->_Config->getBoolean('log_to_browsers')){
			$this->_LoggerInstance->pushHandler(new Monolog\Handler\FirePHPHandler);
			$this->_LoggerInstance->pushHandler(new Monolog\Handler\ChromePHPHandler($this->_debug_level));
		}
		
		if($this->_Config->getBoolean('log_to_file')){
			
			$path = Path::toTemporaryFolder().'/Logs';
			
			if($this->_Config->getString('log_file_path') != ''){
				$path = $this->_Config->getString('log_file_path');
			}
			
			$path = rtrim($path,'/\\');
			
			if($this->_Config->getString('log_file_name') != ''){
				$path .= '/'.$this->_Config->getString('log_file_name');
			} else {
				$path .= '/Access';
			}
			
			$this->_LoggerInstance->pushHandler(new Monolog\Handler\RotatingFileHandler($path,0,$this->_debug_level));
		}
		
		if($this->_Config->getBoolean('with_php_info')){
			$message = $this->_LoggerInstance->pushProcessor(new Monolog\Processor\IntrospectionProcessor);
		}
		
		if($this->_Config->getBoolean('with_web_info')){
			$message = $this->_LoggerInstance->pushProcessor(new Monolog\Processor\WebProcessor);
		}
		
		if($this->_Config->getBoolean('with_memory_info')){
			$message = $this->_LoggerInstance->pushProcessor(new Monolog\Processor\MemoryUsageProcessor);
			$message = $this->_LoggerInstance->pushProcessor(new Monolog\Processor\MemoryPeakUsageProcessor);
		}		
		
	}
	
	public function getInstance(){ return $this; }

	public function __destruct(){
		// unset($this);
	}
	
	public function getLoggerInstance(){ return $this->_LoggerInstance; }
	
	private function log($level, $message, $data){
		$context = array();
		if(!is_null($data)){ 
			if($data instanceof DataObject){
				$context = array($data->toString() => $data->toArray());
			} else if($data instanceof DataCollection){
				$context = array($data->toString() => $data->toArray());
			} else if(is_object($data)){
				$context = array('Object' => $data);
			} else if(is_array($data)){
				$context = $data;
			} else {
				$context = array('data'=>$data);
			}
		}
		
		$this->_LoggerInstance->log($level, $message, $context);
	}
	
	public function debug($message, $data=null){
		$this->log(Monolog\Logger::DEBUG, $message, $data);
		return $this;
	}

	public function info($message, $data=null){
		$this->log(Monolog\Logger::INFO, $message, $data);
		return $this;
	}
	
	public function notice($message, $data=null){
		$this->log(Monolog\Logger::NOTICE, $message, $data);
		return $this;
	}
	
	public function warning($message, $data=null){
		$this->log(Monolog\Logger::WARNING, $message, $data);
		return $this;
	}
	
	public function error($message, $data=null){
		$this->log(Monolog\Logger::ERROR, $message, $data);
		return $this;
	}
	
	public function critical($message, $data=null){
		$this->log(Monolog\Logger::CRITICAL, $message, $data);
		return $this;
	}
	
	public function alert($message, $data=null){
		$this->log(Monolog\Logger::ALERT, $message, $data);
		return $this;
	}
	
	public function emergency($message, $data=null){
		$this->log(Monolog\Logger::EMERGENCY, $message, $data);
		return $this;
	}
	
}
