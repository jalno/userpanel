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
		$absuloteName = $options['name'];
		while(substr($absuloteName, -2) == '[]'){
			$absuloteName = substr($absuloteName, 0, strlen($absuloteName)-2);
		}
		if(!isset($options['error']) or $options['error']){
			$error =  $this->getFormErrorsByInput($absuloteName);
		}else{
			$error = false;
		}
		if(!isset($options['type'])){
			$options['type'] = 'text';
		}
		$code = '';
		if($options['type'] != 'hidden'){
			$code .= '<div class="form-group'.($error ? ' has-error' : '').'">';
			if(isset($options['icon']) and $options['icon']){
				$code .= "<span class=\"input-icon\">";
			}
			if(isset($options['label']) and $options['label'])
				$code .= '<label class="control-label'.(($this->horizontal_form and $this->label_col) ? ' '.$this->label_col : '').'">'.$options['label'].'</label>';

			if(isset($options['input-group']) and $options['input-group']){
				$code .= '<div class="input-group';
				if(isset($options['input-group']['size'])){
					$code .= ' input-group-'.$options['input-group']['size'];
				}
				$code .= '">';
				if(isset($options['input-group']['left'])){
					if(is_string($options['input-group']['left'])){
						$options['input-group']['left'] = array(
							array(
								'type' => 'addon',
								'text' => $options['input-group']['left']
							)
						);
					}
					if(is_array($options['input-group']['left'])){
						foreach($options['input-group']['left'] as $item){
							$code .= $this->buildInputGroupItem($item);
						}
					}
				}
			}

		}
		if(!isset($options['value'])){
			$options['value'] = $this->getDataInput($absuloteName);
		}
		if(!isset($options['class'])){
			$options['class'] = $options['type'] != 'file' ? 'form-control' : '';
		}
		if($this->horizontal_form and $this->input_col and $options['type'] != 'hidden'){
			$code .= "<div class=\"{$this->input_col}\">";
		}
		if(in_array($options['type'], array('radio', 'checkbox'))){
			$options['value'] = $this->getDataForm($absuloteName);
			if(!isset($options['inline'])){
				$options['inline'] = false;
			}
			if(!isset($options['label'])){
				$options['label'] = true;
			}
			//$code .= "<div>";
			foreach($options['options'] as $option){
				$code .= '<div class="'.$options['type'].($options['inline'] ? '-inline' : '').'">';
				if($options['label']){
					$code .= '<label>';
				}
				$code .= "<input type=\"{$options['type']}\" name=\"{$options['name']}\" value=\"{$option['value']}\"";
				if(isset($option['class']) and $option['class']){
					$code .= " class=\"{$option['class']}\"";
				}
				$code .= $this->buildHtmlData($options);
				if(

					$options['value'] !== false and $options['value'] !== null and $options['value'] !== '' and
					(
						(!is_array($options['value']) and $option['value'] == $options['value']) or
						(is_array($options['value']) and in_array($option['value'], $options['value']))
					)
				){
					$code .= " checked";
				}
				$code .= ">";
				if(isset($option['label']))$code .= $option['label'];
				if($options['label']){
					$code .= '</label>';
				}
				$code .= '</div>';
			}
		}elseif($options['type'] == 'select'){
			$code .= "<select";
			$code .= $this->buildHtmlData($options);
		}elseif($options['type'] == 'textarea'){
			$code .= "<textarea";
			$code .= $this->buildHtmlData($options);
			if(isset($options['rows'])){
				$code.= " rows=\"{$options['rows']}\"";
			}
		}else{
			$code .= "<input type=\"{$options['type']}\" value=\"{$options['value']}\" ";
			$code .= $this->buildHtmlData($options);
		}
		if(isset($options['id'])){
			$code .= " id=\"{$options['id']}\"";
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
			if(array_keys($options['options']) == range(0, count($options['options'])-1)){
			 	$code .= utility::selectOptions($options['options'], $options['value']);
			}else{
				$code .= utility::SelectGroupOptions($options['options'], $options['value']);
			}
			$code .="</select>";
		}
		if(in_array($options['type'], array('radio', 'checkbox'))){
			//$code .= "</div>";
		}
		if($options['type'] == 'textarea'){
			$code .= "{$options['value']}</textarea>";
		}

		if($options['type'] != 'hidden'){
			if($error){
				$text = null;
				if(isset($options['error']) and is_array($options['error'])){
					foreach($options['error'] as $type => $value){
						if($type == $error->getCode()){
							if(substr($value, -strlen($error->getCode())) == $error->getCode()){
								$text = translator::trans($value);
							}else{
								$text = $value;
							}
							break;
						}
					}
				}
				if(!$text){
					$text = translator::trans("{$absuloteName}.".$error->getCode());
				}
				if(!$text){
					$text = translator::trans($error->getCode());
				}
				if($text){
					$code .= "<span class=\"help-block\" id=\"{$absuloteName}-error\">{$text}</span>";
				}
			}
			if($this->horizontal_form and $this->input_col and $options['type'] != 'hidden'){
				$code .= "</div>";
			}
			if(isset($options['input-group']) and $options['input-group']){
				if(isset($options['input-group']['right'])){
					if(is_string($options['input-group']['right'])){
						$options['input-group']['right'] = array(
							array(
								'type' => 'addon',
								'text' => $options['input-group']['right']
							)
						);
					}
					if(is_array($options['input-group']['right'])){
						foreach($options['input-group']['right'] as $item){
							$code .= $this->buildInputGroupItem($item);
						}
					}
				}
				$code .= '</div>';
			}
			if(isset($options['icon']) and $options['icon']){
				$code .= "<i class=\"{$options['icon']}\"></i>";
				$code .= "</span>";
			}
			$code .= '</div>';

		}
		echo $code;
	}
	private function buildInputGroupItem($item){
		$code = '';
		if($item['type'] == 'addon'){
			$code .= '<span class="input-group-addon">'.$item['text'].'</span>';
		}elseif($item['type'] == 'checkbox' or $item['type'] == 'radio'){
			$code .= '<span class="input-group-addon">';
			$code .= "<input type=\"{$item['type']}\" name=\"{$item['name']}\" value=\"{$item['value']}\"";
			if(isset($item['class']) and $item['class']){
				$code .= " class=\"{$item['class']}\"";
			}
			$item['absuloteName'] = $item['name'];
			while(substr($item['absuloteName'], -2) == '[]'){
				$item['absuloteName'] = substr($item['absuloteName'], 0, strlen($item['absuloteName'])-2);
			}
			$form_value = $this->getDataForm($item['absuloteName']);
			if(
				(!is_array($form_value) and $item['value'] == $form_value) or
				(is_array($form_value) and in_array($item['value'], $form_value))
			){
				$code .= " checked";
			}
			$code .= ">";
			$code .= "</span>";
		}elseif($item['type'] == 'button' or $item['type'] == 'submit'){
			if(!isset($item['class'])){
				$item['class'] = array('btn');
			}
			if(is_array($item['class'])){
				$item['class'] = implode(" ", $item['class']);
			}
			if(isset($item['dropdown']) and $item['dropdown']){
				if($item['class']){
					$item['class'] .= ' ';
				}
				$item['class'] .= 'dropdown-toggle';
				if(!isset($item['caret']) or $item['caret']){
					$item['text'] =  $item['text'] . ' <span class="caret"></span> ';
				}
			}
			$code .= '<span class="input-group-btn">';
			$code .= "<button class=\"{$item['class']}\" type=\"{$item['type']}\"";
			if(isset($item['name']) and $item['name']){
				$code .= " name=\"{$item['name']}\"";
			}
			if(isset($item['value']) and $item['value']){
				$code .= " value=\"{$item['value']}\"";
			}
			if(isset($item['dropdown']) and $item['dropdown']){
				$code .= " data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\"";
			}
			$code .= $this->buildHtmlData($item);
			$code .= ">";
			if(isset($item['icon']) and $item['icon']){
				$code .= '<i class="'.$item['icon'].'"></i> ';
			}
			$code .= $item['text'];

			$code .= "</button>";
			if(isset($item['dropdown']) and $item['dropdown']){
				$code .= '<ul class="dropdown-menu">';
				foreach($item['dropdown'] as $menu){
					$code .= '<li>';
					if(isset($menu['link'])){
						$code .= '<a href="'.$menu['link'].'"';
						if(isset($menu['class']) and $menu['class']){
							if(is_array($menu['class'])){
								$menu['class'] = implode(" ", $menu['class']);
							}
							$code .= " class=\"{$menu['class']}\"";
						}
						$code .= $this->buildHtmlData($menu);
						$code .='>';
						if(isset($menu['icon']) and $menu['icon']){
							$code .= '<i class="'.$menu['icon'].'"></i> ';
						}
						$code .= $menu['title'].'</a>';
					}
					$code .= '</li>';
				}
				$code .= '</ul>';
			}
			$code .= "</span>";
		}
		return $code;
	}
	private function buildHtmlData($element){
		$code = "";
		if(isset($element['data']) and $element['data']){
			foreach($element['data'] as $key => $value){
				$code .= " data-{$key}=\"{$value}\" ";
			}
		}
		return $code;
	}
}
