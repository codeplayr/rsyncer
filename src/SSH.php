<?php
namespace Codeplayr\Rsyncer;

class SSH{
	
	const EXECUTABLE	= 'executable';
	const USERNAME		= 'username';
	const HOST			= 'host';
	const PORT			= 'port';
	const IDENTITY_FILE	= 'identity_file';
	const REMOTE_SOURCE = 'remote_source';
	
	private $_options = [];
	
	public function __construct( $options = [] ){
		
		$defaults = [
			self::EXECUTABLE	=> 'ssh',
			self::USERNAME		=> null,
			self::HOST			=> null,
			self::PORT			=> 22,
			self::IDENTITY_FILE	=> null,
			self::REMOTE_SOURCE	=> true,
		];
		
		$this->_options = array_merge( $defaults, $options );
	}
	
	public function assemble(){
		return $this->_assembleCommand();
	}
	
	public function getHost(){
		return $this->_options[ self::USERNAME ] . '@' . $this->_options[ self::HOST ];
	}
	
	public function isRemoteSource(){
		return (bool) $this->_options[ self::REMOTE_SOURCE ];
	}
	
	private function _assembleCommand(){
		if( ! is_string( $this->_options[ self::USERNAME ] ) ){
			return null;	
		}
		
		if( ! is_string( $this->_options[ self::HOST ] ) ){
			return null;
		}
		
		if( ! is_int( $this->_options[ self::PORT ] ) ){
			return null;
		}
		
		if( ! is_string( $this->_options[ self::EXECUTABLE ] ) ){
			return null;
		}
		
		$cmd = [];
		$cmd[] = '-e="' . $this->_options[ self::EXECUTABLE ];
		
		if( $this->_options[ self::PORT ] != 22 ){ 
			$cmd[] = "-p {$this->_options[ self::PORT ]}";
		}
		
		if( is_string( $this->_options[ self::IDENTITY_FILE ] ) ){
			if( ! is_readable( $this->_options[ self::IDENTITY_FILE ] ) ){
				return null;
			}
			
			$cmd[] = "-i {$this->_options[ self::IDENTITY_FILE ]}";	
		}
		
		$cmd[] = '"';
		
		return implode(' ',  $cmd );
	}
	
}
