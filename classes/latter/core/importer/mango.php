<?php

class Latter_Core_Importer_Mango extends Latter_Importer
{
	/*
	 * The fields this importer cares about
	 */
	protected $_fields;
	
	/*
	 * The model that's being imported
	 */
	protected $_model;
	
	/*
	 * Add the fields from the model into the form
	 * 
	 * @param An array containing data about the import. For now only the 'model' key will be recognized
	 * 		  but in the future more options may become available.
	 * @param The form to add the fields to.
	 */
	public function add_fields($data, Latter_Form $form)
	{
		$this->_form = $form;
		$model = $this->_model = arr::get($data, 'model');
		
		if( ! $model instanceof Mango)
		{
			return;
		}
				
		$fields = $model->fields();
		$rules = $model->rules();
		
		foreach($fields as $name => $extra)
		{
			$options = array();
			if(arr::get($extra, 'required') == TRUE)
			{
				$options['required'] = TRUE;
			}
			
			$options['rules'] = arr::get($rules, $name);
			$options['value'] = $model->$name;
			$options['importer_field'] = TRUE;
			
			switch(arr::get($extra, 'type'))
			{
				case 'string':
					$this->_fields[] = $name;
					$type = 'text';
					
					if(($name == 'password' && arr::get($extra, 'password') !== FALSE) || arr::get($extra, 'password') === TRUE)
					{
						$type = 'password';
					}
					
					$form->field($name, $type, $options);
					break;
					
				case 'email':
						$this->_fields[] = $name;
						$form->field($name, 'email', $options);
						break;
				
				case 'int':
						$this->_fields[] = $name;
						$form->field($name, 'number', $options);
						break;
					
				case 'date':
						$this->_fields[] = $name;
						$form->field($name, 'date', $options);
				
				case 'enum':
						if(is_array($values = arr::get($extra, 'values')))
						{
							$this->_fields[] = $name;
							$options['options'] = array_flip($values);
							$form->field($name, 'select', $options);
						}
			}
		}
	}
	
	/*
	 * Tell the importer that it's okay to load back the values from the form into the model.
	 */
	public function receive_values()
	{
		foreach($this->_form->values() as $field => $value)
		{
			if(in_array($field, $this->_fields))
			{
				$this->_model->$field = $value;
			}
		}
	}
	
	/*
	 * A simple getter for the model
	 */
	public function model()
	{
		return $this->_model;
	}
}

?>