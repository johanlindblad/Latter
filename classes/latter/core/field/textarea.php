<?php

class Latter_Core_Field_Textarea extends Latter_Field
{
	public function initialize($params)
	{
		$this->_template = 'textarea';
		parent::initialize($params);
	}
}

?>