<?php

namespace packages\userpanel\Logs;

class Panel
{
    public $name;
    public $icon;
    public $priority = 0;
    public $html = '';
    public $size = 12;
    public $title;
    public $buttons = [
        'collapse' => [
            'active' => true,
            'params' => [
                'classes' => ['btn', 'btn-xs', 'btn-link', 'panel-collapse', 'collapses'],
                'link' => '#',
            ],
        ],
    ];

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function setHTML($html)
    {
        $this->html = $html;
    }

    public function setButton($name, $active, $params = [])
    {
        if (!isset($params['classes'])) {
            $params['classes'] = ['btn', 'btn-xs', 'btn-link'];
        }
        if (isset($params['title']) and $params['title']) {
            $params['classes'][] = 'tooltips';
        }
        if (!isset($params['link'])) {
            $params['link'] = '#';
        }
        $button = [
            'active' => $active,
            'params' => $params,
        ];
        $this->buttons[$name] = $button;
    }

    public function setButtonActive($name, $active)
    {
        if (isset($this->buttons[$name])) {
            $this->buttons[$name]['active'] = $active;

            return true;
        }

        return false;
    }

    public function setButtonParam($name, $parameter, $value)
    {
        if (isset($this->buttons[$name])) {
            $this->buttons[$name]['params'][$parameter] = $value;

            return true;
        }

        return false;
    }

    public function unsetButtonParam($name, $parameter)
    {
        if (isset($this->buttons[$name])) {
            unset($this->buttons[$name]['params'][$parameter]);

            return true;
        }

        return false;
    }

    public function hasButtons()
    {
        $have = false;
        foreach ($this->buttons as $btn) {
            if ($btn['active']) {
                $have = true;
                break;
            }
        }

        return $have;
    }

    public function genButtons()
    {
        $buttons = [];
        foreach ($this->buttons as $name => $btn) {
            if ($btn['active']) {
                $buttons[$name] = $btn;
            }
        }
        $code = '';
        if ($buttons) {
            foreach (array_reverse($buttons) as $btn) {
                $code .= '<a';
                if (isset($btn['params']['link']) and $btn['params']['link']) {
                    $code .= ' href="'.$btn['params']['link'].'"';
                }
                if (isset($btn['params']['classes']) and $btn['params']['classes']) {
                    if (is_array($btn['params']['classes'])) {
                        $btn['params']['classes'] = implode(' ', $btn['params']['classes']);
                    }
                    $code .= ' class="'.$btn['params']['classes'].'"';
                }
                if (isset($btn['params']['data']) and $btn['params']['data']) {
                    foreach ($btn['params']['data'] as $name => $value) {
                        $code .= ' data-'.$name.'="'.$value.'"';
                    }
                }
                if (isset($btn['params']['title']) and $btn['params']['title']) {
                    $code .= ' title="'.$btn['params']['title'].'"';
                }
                $code .= '>';
                if (isset($btn['params']['icon']) and $btn['params']['icon']) {
                    $code .= '<i class="'.$btn['params']['icon'].'"></i>';
                }
                $code .= '</a> ';
            }
        }

        return $code;
    }

    public function getHTML()
    {
        $code = '<div class="panel panel-default"><div class="panel-heading">';
        if ($this->icon) {
            $code .= "<i class=\"{$this->icon}\"></i>";
        }
        $code .= $this->title;
        if ($this->hasButtons()) {
            $code .= '<div class="panel-tools">'.$this->genButtons().'</div>';
        }
        $code .= '</div>';
        $code .= '<div class="panel-body form-horizontal">';
        $code .= $this->html;
        $code .= '</div>';
        $code .= '</div>';

        return $code;
    }
}
