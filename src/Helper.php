<?php
namespace Codeplayr\Rsyncer;

class Helper{
	
	public function removeDash( $flag ){
		if( ! is_string( $flag ) || empty($flag)  ){
			throw new \InvalidArgumentException('No String Value');
		}
		
		if( $flag[0] == '-'){
			
			if( strlen( $flag ) > 2 ){
				$left = substr($flag, 0, 2);
				$right = substr($flag, 2);
				$left = str_replace('-', '' , $left);	
				$flag = $left . $right;
			}
			else if( strlen( $flag ) == 2 ){
				$flag = substr($flag, 1);
			}
			else{
				throw new \InvalidArgumentException('Invalid Flag Value');
			}
		
		}
		
		if( empty( $flag ) || $flag[0] == '-' ){
			throw new \InvalidArgumentException('Invalid Assembled Flag');
		}
		
		return $flag;
	}
	
	public function addDash( $flag ){
		if( ! is_string( $flag ) || empty($flag) ){
			throw new \InvalidArgumentException('No String Value');
		}
		
		$flag = $this->removeDash( $flag );
		
		return (strlen($flag) > 1) ? '--' . $flag : '-' . $flag;
	}
	
}
