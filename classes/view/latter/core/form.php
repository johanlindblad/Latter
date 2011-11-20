<?php

class View_Latter_Core_Form extends Kostache
{
	function data()
	{
		$data = $this->data;
		
		return function($var) use($data)
		{
			return arr::get($data, $var);
		};
	}
	
	function fields()
	{
		return $this->fields;
	}
		
	function buttons()
	{
		return $this->buttons;
	}
	
	function open()
	{
		return new Kohana_Mustache('{{> open}}', $this->data, $this->_partials, array(
			'charset' => Kohana::$charset,
		));
	}
	
	function close()
	{
		return new Kohana_Mustache('{{> close}}', $this->data, $this->_partials, array(
			'charset' => Kohana::$charset,
		));
	}
	
	function sub_form()
	{
		return arr::get($this->data, 'sub_form');
	}
		
	function __toString()
	{
		return $this->render();
	}
}

?>