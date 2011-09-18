<?php

/**
 *
 * Class consisting solely of a method to create a form.
 *
 */

class Latter_Core
{
	/*
	 * Create a new form.
	 * @see Latter_Form::__construct for parameters
	 * @returns Latter_Form
	 */
	public static function factory($action = '', $method = 'POST')
	{
		return new Latter_Form($action, $method);
	}
}

?>