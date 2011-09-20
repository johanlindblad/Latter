<?php

class Latter_Core_Button_Cancel extends Latter_Button
{
	/*
	 * Where the link points to
	 */
	protected $_href;
	
	public function initialize($params)
	{
		$this->_template = 'cancel';
		
		// A null value to URL::site gives a link to / so this works fine.
		$this->_href = URL::site(arr::get($params, 'href'));
		
		parent::initialize($params);
	}
	
	public function attributes()
	{
		$attributes = parent::attributes();
		
		$attributes['href'] = $this->_href;
		
		return $attributes;
	}
}

?>