<?php

class Latter_Core_Button_Reset extends Latter_Button
{
	public function initialize($params)
	{
		$this->_template = 'reset';
		parent::initialize($params);
	}
}

?>