<?php

/*
 *
 * A button is something that is rendered in the form but which does not have a value.
 *
 */

class Latter_Core_Button extends Latter_Control
{
	public function initialize($params)
	{
		$this->_view_class = 'View_Latter_Button';
		parent::initialize($params);
	}
	
	public function render()
	{
		$class_name = $this->_view_class;
		$view = new $class_name('latter/button', array('button' => 'latter/button/'.$this->_template));
		
		foreach($this->attributes() as $name => $data)
		{
			$view->$name = $data;
		}
		
		return $view->render();
	}
}

?>