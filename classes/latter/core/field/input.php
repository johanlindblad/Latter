<?php

abstract class Latter_Core_Field_Input extends Latter_Field
{
	/*
	 * The type of input field, as in <input type="$type"
	 */
	protected $_type;
	
	public function initialize($params)
	{
		parent::initialize($params);
		$this->_template = 'input';
	}
	
	public function attributes()
	{
		$array = parent::attributes();
		$array['type'] = $this->_type;
		return $array;
	}
}

?>