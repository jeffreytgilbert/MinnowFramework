<?php

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class MinnowDatabaseHandler extends AbstractProcessingHandler
{
    private 
    	$_initialized = false, 
    	$_pdo,
    	$_statement,
    	$_table_name;

    public function __construct($connection_name = null, $table_name='default', $level = Logger::DEBUG, $bubble = true)
    {
    	$this->_table_name = $table_name;
        $this->pdo = \RuntimeInfo::instance()->getConnections()->MySQL($connection_name)->db_handle;
        parent::__construct($level, $bubble);
    }
	
    protected function write(array $record)
    {
        if (!$this->_initialized) {
            $this->initialize();
        }
		
        $this->statement->execute(array(
            'channel' => $record['channel'],
            'level' => $record['level'],
            'message' => $record['formatted'],
            'time' => $record['datetime']->format('U'),
        ));
    }

    private function initialize()
    {
        $this->pdo->exec(
            'CREATE TABLE IF NOT EXISTS '.$this->_table_name.' '
            .'(channel VARCHAR(255), level INTEGER, message LONGTEXT, time INTEGER UNSIGNED)'
        );
        $this->statement = $this->pdo->prepare(
            'INSERT INTO '.$this->_table_name.' (channel, level, message, time) VALUES (:channel, :level, :message, :time)'
        );
		
        $this->_initialized = true;
    }
}