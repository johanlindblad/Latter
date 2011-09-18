<?php

class View_Latter_Core_Field_Select extends Kostache
{
	public function options()
	{
		$options_formatted = array();
		
		foreach($this->options as $text => $value)
		{
			$option = array('value' => $value, 'text' => $text);
			
			if($option['value'] == $this->value)
			{
				$option['selected'] = TRUE;
			}
			
			$options_formatted[] = $option;
		}
		
		return $options_formatted;
	}
}

?>