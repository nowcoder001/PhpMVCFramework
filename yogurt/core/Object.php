<?php

class Object 
{

 	public function Object() 
 	{
 		
 	}
 	
 	public static function toString() 
 	{
 		return self::__toString();
 	}
 	
 	public function equal($object) 
 	{
 		return $this === $object;
 	}
 	
 	public function getSerialize() 
 	{
 		return serialize($this);
 	}
 
 	public  function __toString() 
 	{
 		return get_class($this)."\n";		
 	}
 	
 	public function __destruct()
 	{
 		
 	}
 	
 }

?>