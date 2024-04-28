<?php

namespace themes\clipone\Views;

use packages\base\Translator;
use packages\base\Utility\Safe;
use themes\clipone\Utility;

trait FormTrait
{
    protected $horizontal_form = false;
    protected $label_col;
    protected $input_col;

    public function setHorizontalForm($label_col, $input_col)
    {
        $label_cols = explode(' ', $label_col);
        foreach ($label_cols as $label_col) {
            $this->label_col .= ' col-'.$label_col;
        }
        $input_cols = explode(' ', $input_col);
        foreach ($input_cols as $input_col) {
            $this->input_col .= ' col-'.$input_col;
        }
        $this->horizontal_form = true;
    }

    public function removeHorizontalForm()
    {
        $this->label_col = '';
        $this->input_col = '';
        $this->horizontal_form = false;
    }

    public function createField($options = [], bool $getHtml = false)
    {
        if (!isset($options['name'])) {
            $options['name'] = '';
        }
        $absuloteName = $options['name'];
        while ('[]' == substr($absuloteName, -2)) {
            $absuloteName = substr($absuloteName, 0, strlen($absuloteName) - 2);
        }
        if (!isset($options['error']) or $options['error']) {
            $error = $this->getFormErrorsByInput($absuloteName);
        } else {
            $error = false;
        }
        if (!isset($options['type'])) {
            $options['type'] = 'text';
        }
        $code = '';
        if ('hidden' != $options['type']) {
            $code .= '<div class="form-group'.($error ? ' has-error' : '').'">';
            if (isset($options['icon']) and $options['icon']) {
                $code .= '<span class="input-icon';
                if (isset($options['right']) and $options['right']) {
                    $code .= ' input-icon-right';
                } elseif (isset($options['left']) and $options['left']) {
                    $code .= ' input-icon-left';
                }
                $code .= '">';
            }
            if (isset($options['label']) and $options['label']) {
                $required = (isset($options['required']) and $options['required']) ? ' required' : '';
                $code .= "<label class=\"control-label{$required}".(($this->horizontal_form and $this->label_col) ? " {$this->label_col}" : '').'">'.$options['label'].'</label>';
            }
            if (isset($options['input-group']) and $options['input-group']) {
                $code .= '<div class="input-group';
                if (isset($options['input-group']['size'])) {
                    $code .= ' input-group-'.$options['input-group']['size'];
                }
                if ($this->horizontal_form and $this->input_col) {
                    $code .= ' '.$this->input_col;
                }
                $code .= '">';
                $isRTL = (bool) Translator::getLang()->isRTL();
                if (isset($options['input-group']['first'])) {
                    $direction = $isRTL ? 'right' : 'left';
                    $options['input-group'][$direction] = $options['input-group']['first'];
                    unset($options['input-group']['first']);
                }
                if (isset($options['input-group']['last'])) {
                    $direction = $isRTL ? 'left' : 'right';
                    $options['input-group'][$direction] = $options['input-group']['last'];
                    unset($options['input-group']['last']);
                }
                if (isset($options['input-group']['left'])) {
                    if (is_string($options['input-group']['left'])) {
                        $options['input-group']['left'] = [
                            [
                                'type' => 'addon',
                                'text' => $options['input-group']['left'],
                            ],
                        ];
                    }
                    if (is_array($options['input-group']['left'])) {
                        foreach ($options['input-group']['left'] as $item) {
                            $code .= $this->buildInputGroupItem($item);
                        }
                    }
                }
            }
        }
        if (!isset($options['value'])) {
            $options['value'] = $this->getDataInput($absuloteName);
        }
        if (!isset($options['class'])) {
            $options['class'] = 'file' != $options['type'] ? 'form-control' : '';
        }
        if ($this->horizontal_form and $this->input_col and 'hidden' != $options['type'] and (!isset($options['input-group']) or !$options['input-group'])) {
            $code .= "<div class=\"{$this->input_col}\">";
        }
        if (in_array($options['type'], ['radio', 'checkbox'])) {
            $options['value_raw'] = $this->getDataForm($absuloteName);
            if (!isset($options['inline'])) {
                $options['inline'] = false;
            }
            if (!isset($options['label'])) {
                $options['label'] = true;
            }
            // $code .= "<div>";
            foreach ($options['options'] as $option) {
                $code .= '<div class="'.$options['type'].($options['inline'] ? '-inline' : '').'">';
                if (isset($option['label'])) {
                    $code .= '<label>';
                }
                $code .= "<input type=\"{$options['type']}\" name=\"{$options['name']}\" value=\"{$option['value']}\"";
                if (isset($option['class']) and $option['class']) {
                    $code .= " class=\"{$option['class']}\"";
                }
                $code .= $this->buildHtmlData($option);
                if (
                    (
                        (false !== $options['value'] and null !== $options['value'] and '' !== $options['value'])
                        or (false !== $options['value_raw'] and null !== $options['value_raw'] and '' !== $options['value_raw'])
                    )
                    and (
                        (!is_array($options['value']) and $option['value'] == $options['value'])
                        or (is_array($options['value_raw']) and in_array($option['value'], $options['value_raw']))
                    )
                ) {
                    $code .= ' checked';
                }
                $code .= '>';
                if (isset($option['label'])) {
                    $code .= $option['label'];
                    $code .= '</label>';
                }
                $code .= '</div>';
            }
        } elseif ('select' == $options['type']) {
            $code .= '<select';
            if (isset($options['multiple']) and $options['multiple']) {
                $code .= ' multiple="multiple"';
            }
            $code .= $this->buildHtmlData($options);
        } elseif ('textarea' == $options['type']) {
            $code .= '<textarea';
            $code .= $this->buildHtmlData($options);
            if (isset($options['rows'])) {
                $code .= " rows=\"{$options['rows']}\"";
            }
        } elseif ('number' == $options['type']) {
            $code .= '<input type="number" value="'.Safe::htmlentities($options['value']).'" ';
            foreach (['step', 'min', 'max'] as $attr) {
                if (isset($options[$attr]) and null !== $options[$attr]) {
                    $code .= "{$attr}=\"{$options[$attr]}\"";
                }
            }
            $code .= $this->buildHtmlData($options);
        } else {
            $code .= "<input type=\"{$options['type']}\" value=\"".Safe::htmlentities($options['value']).'" ';
            $code .= $this->buildHtmlData($options);
        }
        if ('file' == $options['type']) {
            if (isset($options['accept']) and $options['accept']) {
                $code .= 'accept="'.$options['accept'].'"';
            }
        }
        if (isset($options['id'])) {
            $code .= " id=\"{$options['id']}\"";
        }
        if (isset($options['required']) and $options['required']) {
            $code .= ' required';
        }
        if (isset($options['minlength']) and null !== $options['minlength']) {
            $code .= " minlength=\"{$options['minlength']}\"";
        }
        if (isset($options['maxlength']) and null !== $options['maxlength']) {
            $code .= " maxlength=\"{$options['maxlength']}\"";
        }
        if (!in_array($options['type'], ['radio', 'checkbox'])) {
            $code .= " name=\"{$options['name']}\"";
            if (isset($options['ltr']) and $options['ltr']) {
                $options['class'] .= ' ltr';
            }
            if ($options['class']) {
                $code .= " class=\"{$options['class']}\"";
            }
            if (isset($options['placeholder']) and $options['placeholder']) {
                $code .= " placeholder=\"{$options['placeholder']}\"";
            }
            if (isset($options['disabled']) and $options['disabled']) {
                $code .= ' disabled="disabled"';
            }
            if (isset($options['readonly']) and $options['readonly']) {
                $code .= ' readonly="readonly"';
            }
            $code .= '>';
        }
        if ('select' == $options['type']) {
            if (array_keys($options['options']) == range(0, count($options['options']) - 1)) {
                $code .= Utility::selectOptions($options['options'], $options['value']);
            } else {
                $code .= Utility::SelectGroupOptions($options['options'], $options['value']);
            }
            $code .= '</select>';
        }
        if (in_array($options['type'], ['radio', 'checkbox'])) {
            // $code .= "</div>";
        }
        if ('textarea' == $options['type']) {
            $code .= Safe::htmlentities($options['value'], [
                '&' => '&amp;',
                '"' => '&quot;',
                "'" => '&apos;',
                '<' => '&lt;',
                '>' => '&gt;',
            ]).'</textarea>';
        }

        if ('hidden' != $options['type']) {
            if (isset($options['input-group']) and $options['input-group']) {
                if (isset($options['input-group']['right'])) {
                    if (is_string($options['input-group']['right'])) {
                        $options['input-group']['right'] = [
                            [
                                'type' => 'addon',
                                'text' => $options['input-group']['right'],
                            ],
                        ];
                    }
                    if (is_array($options['input-group']['right'])) {
                        foreach ($options['input-group']['right'] as $item) {
                            $code .= $this->buildInputGroupItem($item);
                        }
                    }
                }
                $code .= '</div>';
            }
            if ($error) {
                $text = null;
                if (isset($options['error']) and is_array($options['error'])) {
                    foreach ($options['error'] as $type => $value) {
                        if ($type == $error->getCode()) {
                            if (substr($value, -strlen($error->getCode())) == $error->getCode()) {
                                $text = Translator::trans($value);
                            } else {
                                $text = $value;
                            }
                            break;
                        }
                    }
                }
                if (!$text) {
                    $text = Translator::trans("{$absuloteName}.".$error->getCode());
                }
                if (!$text) {
                    $text = Translator::trans($error->getCode());
                }
                if ($text) {
                    $code .= "<span class=\"help-block\" id=\"{$absuloteName}-error\">{$text}</span>";
                }
            }
            if ($this->horizontal_form and $this->input_col and 'hidden' != $options['type'] and (!isset($options['input-group']) or !$options['input-group'])) {
                $code .= '</div>';
            }
            if (isset($options['icon']) and $options['icon']) {
                $code .= "<i class=\"{$options['icon']}\"></i>";
                $code .= '</span>';
            }
            $code .= '</div>';
        }

        if ($getHtml) {
            return $code;
        }
        echo $code;
    }

    private function buildInputGroupItem($item)
    {
        $code = '';
        $ltr = (isset($item['ltr']) and $item['ltr']) ? ' ltr' : '';
        if ('addon' == $item['type']) {
            $code .= "<span class=\"input-group-addon{$ltr}\"";
            $code .= $this->buildHtmlData($item);
            $code .= ">{$item['text']}</span>";
        } elseif ('checkbox' == $item['type'] or 'radio' == $item['type']) {
            $code .= '<span class="input-group-addon">';
            $code .= "<input type=\"{$item['type']}\" name=\"{$item['name']}\" value=\"{$item['value']}\"";
            if (isset($item['class']) and $item['class']) {
                $code .= " class=\"{$item['class']}\"";
            }
            $item['absuloteName'] = $item['name'];
            while ('[]' == substr($item['absuloteName'], -2)) {
                $item['absuloteName'] = substr($item['absuloteName'], 0, strlen($item['absuloteName']) - 2);
            }
            $form_value = $this->getDataForm($item['absuloteName']);
            if (
                (!is_array($form_value) and $item['value'] == $form_value)
                or (is_array($form_value) and in_array($item['value'], $form_value))
            ) {
                $code .= ' checked';
            }
            $code .= $this->buildHtmlData($item);
            $code .= '>';
            if (isset($item['label'])) {
                $code .= ' <label>'.$item['label'].'</label>';
            }
            $code .= '</span>';
        } elseif ('button' == $item['type'] or 'submit' == $item['type']) {
            if (!isset($item['class'])) {
                $item['class'] = ['btn'];
            }
            if (is_array($item['class'])) {
                $item['class'] = implode(' ', $item['class']);
            }
            if (isset($item['dropdown']) and $item['dropdown']) {
                if ($item['class']) {
                    $item['class'] .= ' ';
                }
                $item['class'] .= 'dropdown-toggle';
                if (!isset($item['caret']) or $item['caret']) {
                    $item['text'] = $item['text'].' <span class="caret"></span> ';
                }
            }
            $code .= '<span class="input-group-btn">';
            $code .= "<button class=\"{$item['class']}\" type=\"{$item['type']}\"";
            if (isset($item['name']) and $item['name']) {
                $code .= " name=\"{$item['name']}\"";
            }
            if (isset($item['value']) and $item['value']) {
                $code .= " value=\"{$item['value']}\"";
            }
            if (isset($item['title']) and $item['title']) {
                $code .= " title=\"{$item['title']}\"";
            }
            if (isset($item['dropdown']) and $item['dropdown']) {
                $code .= ' data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"';
            }
            $code .= $this->buildHtmlData($item);
            $code .= '>';
            if (isset($item['icon']) and $item['icon']) {
                $code .= '<i class="'.$item['icon'].'"></i> ';
            }
            $code .= $item['text'];

            $code .= '</button>';
            if (isset($item['dropdown']) and $item['dropdown']) {
                $code .= '<ul class="dropdown-menu '.(!(bool) Translator::getLang()->isRTL() ? 'dropdown-menu-right' : '').'">';
                foreach ($item['dropdown'] as $menu) {
                    $code .= '<li>';
                    if (isset($menu['link'])) {
                        $code .= '<a href="'.$menu['link'].'"';
                        if (isset($menu['class']) and $menu['class']) {
                            if (is_array($menu['class'])) {
                                $menu['class'] = implode(' ', $menu['class']);
                            }
                            $code .= " class=\"{$menu['class']}\"";
                        }
                        $code .= $this->buildHtmlData($menu);
                        $code .= '>';
                        if (isset($menu['icon']) and $menu['icon']) {
                            $code .= '<i class="'.$menu['icon'].'"></i> ';
                        }
                        $code .= $menu['title'].'</a>';
                    }
                    $code .= '</li>';
                }
                $code .= '</ul>';
            }
            $code .= '</span>';
        } else {
            $absuloteName = $item['name'];
            while ('[]' == substr($absuloteName, -2)) {
                $absuloteName = substr($absuloteName, 0, strlen($absuloteName) - 2);
            }
            if (!isset($item['error']) or $item['error']) {
                $error = $this->getFormErrorsByInput($absuloteName);
            } else {
                $error = false;
            }
            if (!isset($item['value'])) {
                $item['value'] = $this->getDataInput($absuloteName);
            }
            $code .= '<span class="input-group-btn form-group'.(($item['name'] and $error and $error->input == $item['name']) ? ' has-error' : '').'">';
            if ('select' == $item['type']) {
                $code .= '<select';
            } elseif ('input' == $item['type']) {
                $code .= "<input type=\"{$item['type']}\" value=\"{$item['value']}\" ";
            }
            $code .= $this->buildHtmlData($item);
            $code .= " name=\"{$item['name']}\"";
            if (!isset($item['class'])) {
                $item['class'] = 'form-control';
            }
            if (isset($item['ltr']) and $item['ltr']) {
                $item['class'] .= ' ltr';
            }
            if ($item['class']) {
                $code .= " class=\"{$item['class']}\"";
            }
            if (isset($item['placeholder']) and $item['placeholder']) {
                $code .= " placeholder=\"{$item['placeholder']}\"";
            }
            if (isset($item['disabled']) and $item['disabled']) {
                $code .= ' disabled="disabled"';
            }
            if (isset($item['readonly']) and $item['readonly']) {
                $code .= ' readonly="readonly"';
            }
            $code .= '>';
            if ('select' == $item['type']) {
                if (array_keys($item['options']) == range(0, count($item['options']) - 1)) {
                    $code .= Utility::selectOptions($item['options'], $item['value']);
                } else {
                    $code .= Utility::SelectGroupOptions($item['options'], $item['value']);
                }
                $code .= '</select>';
            }
            $code .= '</span>';
            if ($error) {
                $text = null;
                if (isset($item['error']) and is_array($item['error'])) {
                    foreach ($item['error'] as $type => $value) {
                        if ($type == $error->getCode()) {
                            if (substr($value, -strlen($error->getCode())) == $error->getCode()) {
                                $text = Translator::trans($value);
                            } else {
                                $text = $value;
                            }
                            break;
                        }
                    }
                }
                if (!$text) {
                    $text = Translator::trans("{$absuloteName}.".$error->getCode());
                }
                if (!$text) {
                    $text = Translator::trans($error->getCode());
                }
                if ($text) {
                    $code .= "<span class=\"help-block\" id=\"{$absuloteName}-error\">{$text}</span>";
                }
            }
        }

        return $code;
    }

    private function buildHtmlData($element)
    {
        $code = '';
        if (isset($element['data']) and $element['data']) {
            foreach ($element['data'] as $key => $value) {
                $code .= " data-{$key}='{$value}' ";
            }
        }

        return $code;
    }
}
