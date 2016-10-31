<?php
namespace Codeplayr\Rsyncer;

class Option{
	
	const ARCHIVE			= 'archive';	//equals -rlptgoD (no -H,-A,-X)
	const RECURSIVE			= 'recursive';
	const LINKS				= 'links';
	const PERMS				= 'perms';
	const TIMES				= 'times';
	const GROUP				= 'group';
	const OWNER				= 'owner';
	const CHECKSUM			= 'checksum';
	const HUMAN_READABLE	= 'human_readable';
	const DEVICES			= 'devices';
	const DRY_RUN			= 'dry_run';
	const VERBOSE			= 'verbose';
	const COMPRESS			= 'compress';
	const PROGRESS			= 'progress';
	const DELETE			= 'delete';
	const FILES_FROM		= 'files_from';
	const EXCLUDE_FROM		= 'exclude_from';
	const LOG_FILE			= 'log_file';
	
	private $_options = [];
	private $_args = [];
	
	private $_options_map = [
		self::ARCHIVE			=> 'a',
		self::RECURSIVE			=> 'r',
		self::LINKS				=> 'l',
		self::PERMS				=> 'p',
		self::TIMES				=> 't',
		self::GROUP				=> 'g',
		self::OWNER				=> 'o',
		self::CHECKSUM			=> 'c',
		self::HUMAN_READABLE	=> 'h',
		self::DEVICES			=> 'D',
		self::DRY_RUN			=> 'n',
		self::VERBOSE			=> 'v',
		self::COMPRESS			=> 'z',
		self::PROGRESS			=> 'progress',
		self::DELETE			=> 'delete',
		self::FILES_FROM		=> 'files-from',
		self::EXCLUDE_FROM		=> 'exclude-from',
		self::LOG_FILE			=> 'log-file',
	];
	
	public function __construct( $options = [] ){
		
		if( ! is_array( $options ) ) return;
		
		foreach( $options as $key => $val ){
			$flag = $this->_options_map[ $key ];
			if( $flag && $val === true ){
				$this->addOption( $flag );
			}
		}
		
		if( key_exists( self::FILES_FROM, $options ) ){
			$this->addArgument( $this->_options_map[ self::FILES_FROM ], $options[ self::FILES_FROM ] );
		}
		
		if( key_exists( self::EXCLUDE_FROM, $options ) ){
			$this->addArgument( $this->_options_map[ self::EXCLUDE_FROM ], $options[ self::EXCLUDE_FROM] );
		}		
		
		if( key_exists( self::LOG_FILE, $options ) ){
			$this->addArgument( $this->_options_map[ self::LOG_FILE ], $options[ self::LOG_FILE ] );
		}  
	}
	
	public function assemble(){
		$flags = $this->_assembleFlags();
		$args = $this->_assembleArgs();

		return $flags . ' ' . $args;
	}
	
	public function addOption( $flag ){
		if( ! is_string( $flag ) ) return $this;
		
		if( strlen( $flag ) > 2 ){
			$left = substr($flag, 0, 2);
			$right = substr($flag, 2);
			$left = str_replace('-', '' , $left);	
			$flag = $left . $right;
		}
		
		if( $flag == 'a' ) $this->_options = array_merge( $this->_options, ['r', 'l', 'p', 't', 'g', 'o', 'D' ] );
		else $this->_options[] = $flag;
		
		return $this;
	}
	
	public function removeOption( $flag ){
		if( ! is_string( $flag ) ) return $this ;
		$idx = array_search( $flag, $this->_options );
		if( $idx >= 0 ){
			unset( $this->_options[ $idx ] );
		}  
		
		return $this;
	}
	
	public function addArgument( $flag, $value ){
		if( !is_string( $flag ) ) return;
		
		$this->_args[ $flag ] = $value;
		
		return $this;
	}
	
	private function _assembleArgs(){
		$s = [];
		foreach( $this->_args as $k=>$v ){
			$s[] = '--' . $k . '=' . escapeshellarg($v);
		}
		return implode(' ', $s);
	}
	
	private function _assembleFlags(){
		$unique_opts = array_unique( $this->_options );
		
		$flags_single = [];
		$flags_multi = [];
		
		foreach( $unique_opts as $flag ){
			if( strlen($flag) == 1 ) $flags_single[] = $flag;
			else $flags_multi[] = '--' . $flag;
		}
		
		$s = [];
		$s[] = '-' . implode('', $flags_single);
		$s[] = implode(' ', $flags_multi); 		
		
		return implode(' ', $s);
	}	
}
