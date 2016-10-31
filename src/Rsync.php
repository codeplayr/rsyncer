<?php
namespace Codeplayr\Rsyncer;

use \Codeplayr\Rsyncer\Option;
use \Codeplayr\Rsyncer\Message;
use \Codeplayr\Rsyncer\SSH;

class Rsync{
	
	const EXECUTABLE	= 'executable';
	const SHOW_OUTPUT	= 'show_output';
	
	private $_message = null;
	private $_valid = true;
	private $_option = null;
	private $_ssh = null;
	private $_conf = null;
	
	public function __construct( Option $options = null, SSH $ssh = null, $conf = [], $message = null ){
		
		$defaults = [
			self::EXECUTABLE	=> 'rsync',
			self::SHOW_OUTPUT	=> false,
		];
		
		$this->_conf = array_merge( $defaults, $conf );
		
		$this->_message = ($message instanceof Message ) ? $message : new Message();
		$this->_option = $options;
		$this->_ssh = $ssh;
	}
	
	public function sync( $source, $destination ){
		$cmd = $this->getCommand( $source, $destination );
		
		if( ! $this->_valid ) return false;
		
		return $this->_execute( $cmd );
	}
	
	public function getMessage(){
		return $this->_message;
	}
	
	public function getCommand( $source, $destination ){
		if( ! isset( $this->_conf[ self::EXECUTABLE ] ) ){
			if( ! $this->_commandExists('rsync') ){
				$this->_message->store( $this->_message->getMessage(Message::INVALID_EXECUTABLE) );
				$this->_valid = false;
			}
		}

		return $this->_assembleCommand( $source, $destination ); 		
	}
	
	private function _execute( $cmd ){
		$b = true;
		
		if( (bool)$this->_conf[ self::SHOW_OUTPUT ] ){
			$b = $this->_executeWithOutput( $cmd );
		}
		else{
			exec($cmd, $output, $error_val);
			if( $error_val !== 0 ) $b = false;
		}
		
		if( $b ) $this->_message->store( $this->_message->getMessage( Message::SUCCESS_EXECUTE ));
		else $this->_message->store( $this->_message->getMessage( Message::ERROR_EXECUTE ));
		
		return $b;
	}
	
	private function _executeWithOutput( $command ){
		echo "Execute: " . $command . PHP_EOL;
		if( ($fp = popen( $command, "r" )) ){
			while( ! feof( $fp ) ){
				echo fread( $fp, 1024 );
				flush();
			}
			fclose($fp);
		}
		else return false;
		return true;
	}
	
	private function _assembleCommand( $source, $destination ){
		
		$cmd = [];
		
		$cmd[] = $this->_conf[ self::EXECUTABLE ];
		
		if( $this->_option ){
			$cmd[] = $this->_option->assemble(); 
		}
		
		if( is_null( $this->_ssh ) ){
			$cmd[] = escapeshellarg($source);
			$cmd[] = escapeshellarg($destination);	
		}
		else{
			$ssh_cmd = $this->_assembleSSHCommand( $source, $destination ); 
			if(  ! $ssh_cmd ) $this->_valid = false;
			$cmd[] = $ssh_cmd;
		}
		
		return implode(' ', $cmd);
	}
	
	private function _assembleSSHCommand( $source, $destination ){
		$cmd = null;
		
		$ssh_command = $this->_ssh->assemble();
		if( ! $ssh_command ){
			$this->_valid = false;
			$this->_message->store( $this->_message->getMessage( Message::INVALID_SSH ));
		}
		else{
			$cmd = [];
			$cmd[] = $ssh_command;
			
			if( $this->_ssh->isRemoteSource() ){
				$cmd[] = $this->_ssh->getHost() . ':' . escapeshellarg($source);
				$cmd[] = escapeshellarg($destination);	
			}
			else{
				$cmd[] = escapeshellarg($source);
				$cmd[] = $this->_ssh->getHost() . ':' . escapeshellarg($destination);	
			}
		}
		
		return is_array( $cmd ) ? implode(' ', $cmd) : null;
	}
	
	private function _commandExists( $cmd ){
		$whereCmd = (substr( strtolower(PHP_OS), 0, 3) == 'win') ? 'where' : 'which';
		$val = shell_exec("$whereCmd $cmd");;
		return ( empty( $val ) ) ? false : true;	
	}	
	
}
