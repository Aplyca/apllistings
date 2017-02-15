<?php

class ExpirationLogger{

	var $object;
	
	static function logInvalidObject($objectID)
	{
		self::writeLog();		
	}
	
	static function writeLog()
	{
		// logif for writing file, fetch loggin folder etc
	}

			
}

?>