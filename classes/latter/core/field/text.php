<?php

class Latter_Core_Field_Text extends Latter_Field_Input
{
	public function initialize($params)
	{
		$this->_type = 'text';
		parent::initialize($params);
	}
}

?>