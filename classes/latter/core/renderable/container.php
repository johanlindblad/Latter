<?php

class Latter_Core_Renderable_Container
{
	public $contents;

	public function __construct(Array $contents)
	{
		$this->contents = $contents;
	}
	
	public function __isset($name)
	{
		if(arr::get($this->contents, $name))
		{
			return TRUE;
		}
	}
	
	public function __get($name)
	{
		return arr::get($this->contents, $name);
	}
		
	public function __toString()
	{
		$output = '';
		
		foreach($this->contents as $item)
		{
			$output .= $item->render();
		}
		
		return $output;
	}
	
	public function as_non_associative_array()
	{
		$array = array();
		
		foreach($this->contents as $item)
		{
			$array[] = $item;
		}
		
		return $array;
	}
}

?>