<?php
namespace themes\clipone\views;
use \packages\base\translator;
use \themes\clipone\utility;
use \themes\clipone\viewTrait;
trait formTrait{
	protected $horizontal_form = false;
	protected $label_col;
	protected $input_col;
	public function setHorizontalForm($label_col, $input_col){
		$label_cols = explode(' ', $label_col);
		foreach($label_cols as $label_col){
			$this->label_col = 'col-'.$label_col;
		}
		$input_cols = explode(' ', $input_col);
		foreach($input_cols as $input_col){
			$this->input_col = 'col-'.$input_col;
		}
		$this->horizontal_form = true;
	}
	public function createField($options = array()){
		if(!isset($options['name'])){
			$options['name'] = '';
		}
		if(!isset($options['error']) or $options['error']){
			$error =  $this->getFromErrorsByInput($options['name']);
		}else{
			$error = false;
		}
		$code = '<div class="form-group'.($error ? ' has-error' : '').'">';
		if(isset($options['icon']) and $options['icon']){
			$code .= "<span class=\"input-icon\">";
		}
		if(isset($options['label']) and $options['label'])
			$code .= '<label class="control-label'.(($this->horizontal_form and $this->label_col) ? ' '.$this->label_col : '').'">'.$options['label'].'</label>';
		if(!isset($options['type'])){
			$options['type'] = 'text';
		}
		if(!isset($options['value'])){
			$options['value'] = $this->getDataForm($options['name']);
		}
		if(!isset($options['class'])){
			$options['class'] = 'form-control';
		}
		if($this->horizontal_form and $this->input_col){
			$code .= "<div class=\"{$this->input_col}\">";
		}
		if(in_array($options['type'], array('radio', 'checkbox'))){
			if(!isset($options['inline'])){
				$options['inline'] = false;
			}
			$code .= "<div>";
			foreach($options['options'] as $option){
				$code .= '<label class="'.$options['type'].($options['inline'] ? '-inline' : '').'">';
				$code .= "<input type=\"{$options['type']}\" name=\"{$options['name']}\" value=\"{$option['value']}\"";
				if(isset($option['class']) and $option['class']){
					$code .= " class=\"{$option['class']}\"";
				}
				if($option['value'] == $options['value']){
					$code .= " checked";
				}
				$code .= ">".$option['label'];
				$code .= '</label>';
			}
		}elseif($options['type'] == 'select'){
			$code .= "<select";
		}else{
			$code .= "<input type=\"{$options['type']}\" value=\"{$options['value']}\"";
		}
		if(!in_array($options['type'], array('radio', 'checkbox'))){
			$code .= " name=\"{$options['name']}\"";
			if(isset($options['ltr']) and $options['ltr']){
				$options['class'] .= " ltr";
			}
			if($options['class']){
				$code .= " class=\"{$options['class']}\"";
			}
			if(isset($options['placeholder']) and $options['placeholder']){
				$code .= " placeholder=\"{$options['placeholder']}\"";
			}
			if(isset($options['disabled']) and $options['disabled']){
				$code .= " disabled=\"disabled\"";
			}
			if(isset($options['readonly']) and $options['readonly']){
				$code .= " readonly=\"readonly\"";
			}
			$code .= ">";
		}
		if($options['type'] == 'select'){
		 	$code .= utility::selectOptions($options['options'], $options['value']);
			$code .="</select>";
		}
		if(in_array($options['type'], array('radio', 'checkbox'))){
			$code .= "</div>";
		}
		if(isset($options['icon']) and $options['icon']){
			$code .= "<i class=\"{$options['icon']}\"></i>";
			$code .= "</span>";
		}
		if($error){
			$text = null;
			if(isset($options['error']) and is_array($options['error'])){
				foreach($options['error'] as $type => $value){
					if($type == $error->error){
						if(substr($value, -strlen($error->error)) == $error->error){
							$text = translator::trans($value);
						}else{
							$text = $value;
						}
						break;
					}
				}
			}
			if(!$text){
				$text = translator::trans("{$options['name']}.{$error->error}");
			}
			if(!$text){
				$text = translator::trans($error->error);
			}
			if($text){
				$code .= "<span class=\"help-block\" id=\"{$options['name']}-error\">{$text}</span>";
			}
		}
		if($this->horizontal_form and $this->input_col){
			$code .= "</div>";
		}
		$code .= '</div>';
		echo $code;
	}
}
