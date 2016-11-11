<?php
namespace Codeplayr\Rsyncer;

class Helper{
	
	public function removeDash( $flag ){
		if( ! is_string( $flag ) ){
			throw new \InvalidArgumentException('No String Value');
		}
		
		if( strlen( $flag ) > 2 && $flag[0] == '-' ){
			$left = substr($flag, 0, 2);
			$right = substr($flag, 2);
			$left = str_replace('-', '' , $left);	
			$flag = $left . $right;
		}
		return $flag;
	}
	
	public function addDash( $flag ){
		if( ! is_string( $flag ) ){
			throw new \InvalidArgumentException('No String Value');
		}
		
		return (strlen($flag) > 1) ? '--' . $flag : '-' . $flag;
	}
	
}
