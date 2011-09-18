<?php

class Latter_Core_Field_Password extends Latter_Field_Input
{
	public function initialize($params)
	{
		$this->_type = 'password';
		parent::initialize($params);
	}
}

?>