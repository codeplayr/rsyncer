<?php
namespace Codeplayr\Rsyncer;

class Message{
	
	const INVALID_EXECUTABLE= 'INVALID_EXECUTABLE';
	const INVALID_FROM_FILE	= 'INVALID_FROM_FILE';
	const SUCCESS_EXECUTE	= 'SUCCESS_EXECUTE';
	const ERROR_EXECUTE		= 'ERROR_EXECUTE';
	const INVALID_SSH		= 'INVALID SSH';
	
	private $_templates = [
		self::INVALID_EXECUTABLE=> 'Invalid executable',
		self::INVALID_FROM_FILE	=> 'File in from-file option not exists',
		self::SUCCESS_EXECUTE 	=> 'Success executing command',
		self::ERROR_EXECUTE 	=> 'Error executing command',
		self::INVALID_SSH 		=> 'Invalid SSH config',
	];
	
	private $_stored_messages = [];
	
	public function getMessage( $type ){
		return isset( $this->_templates[ $type ]) ? $this->_templates[ $type ] : '';
	}
	
	public function store( $message ){
		$this->_stored_messages[] = $message;
	}
	
	public function toString(){
		$m = [];
		foreach( $this->_stored_messages as $message ) $m[] = $message;
		return implode(' ' , $m);
	}
	
}
