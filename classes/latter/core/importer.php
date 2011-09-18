<?php

/*
 *
 * An abstract class that represents an importer which will create fields and then receive the values.
 *
 */

abstract class Latter_Core_Importer
{
	/*
	 * The form the importer belongs to
	 */
	protected $_form;
	
	/*
	 * Add the fields from the model into the form
	 * 
	 * @param An array containing data about the import. For now only the 'model' key will be recognized
	 * 		  but in the future more options may become available.
	 * @param The form to add the fields to.
	 */
	abstract public function add_fields($data, Latter_Form $form);
	
	/*
	 * Tell the importer that it's okay to load back the values from the form into the model.
	 */
	abstract public function receive_values();
}

?>