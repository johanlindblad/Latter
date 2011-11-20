<?php

class View_Latter_Core_Field extends Kostache
{
	public function field_name()
	{
		return strtr(':form_name[:field_name]', array(
			':form_name' => $this->form->full_name(),
			':field_name' => $this->name
		));
	}
}

?>