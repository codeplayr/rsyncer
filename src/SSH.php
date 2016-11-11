<?php
namespace Codeplayr\Rsyncer;

use \Codeplayr\Rsyncer\Helper;

class SSH{
	
	const EXECUTABLE	= 'executable';
	const USERNAME		= 'username';
	const HOST			= 'host';
	const PORT			= 'port';
	const IDENTITY_FILE	= 'identity_file';
	const REMOTE_SOURCE = 'remote_source';
	
	private $_options = [];
	private $_flags = [];
	private $_helper = null;
	
	public function __construct( $options = [] ){
		
		$this->_helper = new Helper();
		
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
	
	public function addFlag( $name, $value = null ){
		$name = $this->_helper->removeDash( $name );
		$this->_flags[ $name ] = $value;
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
			$cmd[] = "-i {$this->_options[ self::IDENTITY_FILE ]}";	
		}
		
		foreach( $this->_flags as $name => $value ){
			$name = $this->_helper->addDash( $name );
			
			$cmd[] = ( is_null( $value ) ) ? $name : $name . ' ' . $value;
		}
		
		return implode(' ',  $cmd ) . '"';
	}
	
}
