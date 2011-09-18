<?php

class Latter_Core_Field_Date extends Latter_Field_Input
{
	public function initialize($params)
	{
		$this->_template = 'input';
		$this->_type = 'date';
		parent::initialize($params);
	}
}

?>