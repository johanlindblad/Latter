<?php

class Latter_Core_Button_Submit extends Latter_Button
{
	public function initialize($params)
	{
		$this->_template = 'submit';
		parent::initialize($params);
	}
}

?>