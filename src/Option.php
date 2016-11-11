<?php
namespace Codeplayr\Rsyncer;

use \Codeplayr\Rsyncer\Helper;

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
	
	private $_flags = [];
	private $_extras = [];
	private $_helper = null;
	
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
		
		$this->_helper = new Helper();
		
		if( ! is_array( $options ) ) return;
		
		foreach( $options as $key => $val ){
			$flag = $this->_options_map[ $key ];
			if( $flag && $val === true ){
				$this->addFlag( $flag );
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
		$opts = implode(' ', $this->_extras);
		
		return trim( $flags . ' ' . $opts );
	}
	
	public function addFlag( $flag ){
		if( ! is_string( $flag ) ){
			throw new \InvalidArgumentException('No String Value');
		}
		
		$flag = $this->_helper->removeDash( $flag );
		
		if( $flag == 'a' ) $this->_flags = array_merge( $this->_flags, ['r', 'l', 'p', 't', 'g', 'o', 'D' ] );
		else $this->_flags[] = $flag;
		
		return $this;
	}
	
	public function removeFlag( $flag ){
		if( ! is_string( $flag ) ){
			throw new \InvalidArgumentException('No String Value');
		}
		
		$flag = $this->_helper->removeDash( $flag );
		
		$idx = array_search( $flag, $this->_flags );
		if( $idx >= 0 ){
			unset( $this->_flags[ $idx ] );
		}  
		
		return $this;
	}
	
	public function addArgument( $name, $value, $useEqualDelimiter = true ){
		if( ! is_string( $name ) ){
			throw new \InvalidArgumentException('No String Value');
		}
				
		$name = $this->_helper->removeDash( $name );
		$name = $this->_helper->addDash( $name );
		
		$this->_extras[] =  ( ! $useEqualDelimiter )? $name . ' ' . escapeshellarg( $value )
													: $name . '=' . escapeshellarg( $value );
		
		return $this;
	}
	
	private function _assembleFlags(){
		$unique_opts = array_unique( $this->_flags );
		
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
