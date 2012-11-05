<?php

class VStringHelper {

    public static function generatePassword($count = 6)
    {
    	$str = '0,1,2,3,4,5,6,7,8,9';

		$arr = explode(',', preg_replace( '#[\n\r\t\s]#', '', $str ) );
		$c = count( $arr );

		$password = '';

		for( $i = 0; $i <= $count; $i++ )
			$password .= $arr[ mt_rand( 0, $c - 1 ) ];

		return $password;
	}

}
