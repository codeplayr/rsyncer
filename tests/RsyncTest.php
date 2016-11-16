<?php

use \Codeplayr\Rsyncer\Option;
use \Codeplayr\Rsyncer\SSH;
use \Codeplayr\Rsyncer\Rsync;
use \Codeplayr\Rsyncer\Helper;

class RsyncTest extends PHPUnit_Framework_TestCase{

	protected function setUp(){}
	
	public function test_rsync_command_assembling_with_options_returns_valid_command(){
		
		$source = __DIR__ . '/src/';
		$destination = __DIR__ . '/backup/';

		$files_from = __DIR__ . '/rules.txt';
		
		$option = new Option([
			Option::FILES_FROM	=> $files_from,
			Option::ARCHIVE		=> false,
			Option::LINKS		=> true,
			Option::TIMES		=> true,
			Option::RECURSIVE	=> true,
			Option::VERBOSE		=> true,
			Option::COMPRESS	=> true,
			Option::CHECKSUM	=> true,
			Option::DRY_RUN		=> false,
		]);

		$identity_file = '/path/to/private/key';
		
		$ssh = new SSH([
			SSH::USERNAME		=> 'root',
			SSH::HOST			=> '192.168.1.101',
			SSH::PORT			=> 22,
			SSH::IDENTITY_FILE	=> $identity_file,
		]);

		$rsync = new Rsync( $option, $ssh );
		
		$this->assertInstanceOf('\Codeplayr\Rsyncer\Rsync', $rsync);
		
		$cmd = $rsync->getCommand( $source, $destination );
		
		$this->assertContains('-ltrvzc', $cmd);
		$this->assertContains("--files-from=\"{$files_from}\"", $cmd);
		$this->assertContains("-e=\"ssh -i {$identity_file}\"", $cmd);
	}
	
	public function test_rsync_execution_with_dryrun_returns_success(){
		$source =  realpath( __DIR__ . '/../src/' );
		$destination = __DIR__;
		
		function transform( $path ){
			$pos = strpos( $path, ':');
			$left = substr($path, 0, $pos);
			$right = substr($path, $pos + 1 );
			return str_replace('\\', '/', "/cygdrive/" . $left . $right);
		}
		
		if(substr( strtolower(PHP_OS), 0, 3) == 'win'){
			$source = transform( $source );
			$destination = transform( $destination );
		}
		
		$option = new Option([
			Option::ARCHIVE		=> true,
			Option::DRY_RUN		=> true,
		]);

		$rsync = new Rsync( $option, null, [Rsync::SHOW_OUTPUT	=> true]);
		
		$this->assertTrue( $rsync->sync( $source, $destination ) );
	}	
	
	public function test_flags_with_dash(){
		$helper = new Helper();
		$this->assertEquals('a', $helper->removeDash('-a'));
		$this->assertEquals('archive', $helper->removeDash('--archive'));
		$this->assertEquals('archive', $helper->removeDash('archive'));
		$this->assertEquals('-a', $helper->addDash('a'));
		$this->assertEquals('--archive', $helper->addDash('archive'));
		$this->assertEquals('--archive', $helper->addDash('--archive'));
	}
		
	/**
	 * @dataProvider invalid_flags_dataprovider
	 */
	public function test_invalid_flag_returns_exception( $flag ){
		$this->setExpectedException('InvalidArgumentException');
		
		$helper = new Helper();
		$helper->removeDash( $flag );
		$helper->addDash( $flag );
	}
	
	public function invalid_flags_dataprovider() {
		return [
			[0], 
			[''], 
			['-'], 
			['--']
		];
	}
	
}