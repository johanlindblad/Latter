<?php

class Latter_Core_Field_Email extends Latter_Field_Input
{
	public function initialize($params)
	{
		$this->_type = 'email';
		parent::initialize($params);
	}
}

?>