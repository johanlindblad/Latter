<?php

/*
 *
 * A control is something that renders on the screen, be it a button or a field.
 * What they have in common will be defined here but they will each have their own
 * functionality implemented in their respective classes.
 *
 */

abstract class Latter_Core_Control
{
	/*
	 * The control's name
	 */
	protected $_name;

	/*
	 * The control's label.
	 * Note that this isn't strictly for a <label>-tag, in the case of a button this will
	 * be the text on the button itself.
	 */
	protected $_label;
	
	/*
	 * The view class that's supposed to be used
	 */
	protected $_view_class;
	
	/*
	 * The template file that's supposed to be used
	 */
	protected $_template;
	
	/*
	 * The control's parent form
	 */
	protected $_form;

	/*
	 * Constructor.
	 *
	 * @param string $name field name
	 * @param array $params extra params to be passed on to the initialize function
	 */
	public function __construct($name, $params = array())
	{
		$this->_name = $this->_label = $name;
		
		if(arr::get($params, 'label'))
		{
			$this->_label = $params['label'];
		}
		
		if($form = arr::get($params, 'form'))
		{
			$this->_form = $form;
		}
		else
		{
			throw new Kohana_Exception('The control needs to be provided with its parent form.');
		}
		
		$this->_view_class = NULL;
		$this->_template = NULL;
		$this->initialize($params);
	}

	/*
	 * Any extra stuff that needs to run after the constructor
	 */
	public function initialize($params)
	{
	}
	
	/*
	 * Getter for name
	 */ 
	public function name()
	{
		return $this->_name;
	}
	
	/*
	 * Render the control according to the _template and _view_class properties. attributes() will be run.
	 */
	abstract public function render();
	
	public function __toString()
	{
		return $this->render();
	}
	
	protected function attributes()
	{
		return array(
			'name' => $this->_name,
			'label' => $this->_label,
			'form' => $this->_form,
		);
	}
}

?>