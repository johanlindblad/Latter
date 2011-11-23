<?php

class View_Latter_Core_Button extends View_Latter_Field
{
	public function field_name()
	{
		return strtr(':form_name[:field_name]', array(
			':form_name' => $this->form->name(),
			':field_name' => $this->name
		));
	}
}

?>