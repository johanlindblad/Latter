<?php

class Latter_Core_Field_Select extends Latter_Field
{
	/*
	 * The available options
	 */
	protected $_options;
	
	public function initialize($params)
	{
		parent::initialize($params);

		if($options = arr::get($params, 'options'))
		{
			$this->_options = $options;
		}
		
		$this->_view_class = 'View_Latter_Field_Select';
		$this->_template = 'select';
	}
	
	public function attributes()
	{
		$array = parent::attributes();
		$array['options'] = $this->_options;
		return $array;
	}
}

?>