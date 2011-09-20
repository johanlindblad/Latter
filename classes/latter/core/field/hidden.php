<?php

class Latter_Core_Field_Hidden extends Latter_Field_Input
{
	public function initialize($params)
	{
		$this->_type = 'hidden';
		$this->_label = '';
		parent::initialize($params);
	}
}

?>