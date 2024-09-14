<?php

namespace themes\clipone;

use packages\base\Json;

class Utility
{
    public static function switchcase($item, array $items)
    {
        foreach ($items as $return => $cases) {
            if (is_array($cases)) {
                foreach ($cases as $case) {
                    if ($item == $case) {
                        return $return;
                    }
                }
            } elseif ($cases == $item) {
                return $return;
            }
        }

        return false;
    }

    public static function selectOptions($rows, $selected = null)
    {
        $html = '';
        foreach ($rows as $option) {
            if (!is_array($option)) {
                $option = [
                    'title' => $option,
                    'value' => $option,
                ];
            }
            $data = '';
            if (isset($option['data']) and $option['data']) {
                if (is_array($option['data'])) {
                    foreach ($option['data'] as $key => $val) {
                        $data .= ' ';
                        $data .= "data-{$key}='";
                        if (is_array($val) or is_object($val)) {
                            $data .= Json\Encode($val);
                        } else {
                            $data .= $val;
                        }
                        $data .= "'";
                    }
                } else {
                    $data .= " data='";
                    if (is_array($option['data']) or is_object($option['data'])) {
                        $data .= Json\Encode($option['data']);
                    } else {
                        $data .= $option['data'];
                    }
                    $data .= "'";
                }
            }
            $option['title'] = htmlentities($option['title'] ?? '');
            $isSelected = is_array($selected) ? in_array($option['value'], $selected) : (false !== $selected and $selected == $option['value']);
            $isDisabled = (isset($option['disabled']) and $option['disabled']);
            $html .= "<option value=\"{$option['value']}\"{$data}".($isSelected ? ' selected' : '').($isDisabled ? ' disabled="disabled"' : '').">{$option['title']}</option>";
        }

        return $html;
    }

    public static function SelectGroupOptions($groups, $selected = null)
    {
        $html = '';
        foreach ($groups as $title => $rows) {
            $html .= "<optgroup label=\"{$title}\">";
            $html .= self::selectOptions($rows, $selected);
            $html .= '</optgroup>';
        }

        return $html;
    }

    public static function radiobox($rows, $selected = null)
    {
        $html = '';
        foreach ($rows as $value => $title) {
            if (is_array($title)) {
                $values = array_column($rows, 'value');
                $titles = array_column($rows, 'title');

                return self::radiobox(array_combine($values, $titles), $selected);
            } else {
                $html .= "<option value=\"{$value}\"".($selected == $value ? ' selected' : '').">{$title}</option>";
            }
        }

        return $html;
    }

    public static function dateFormNow($time)
    {
        $thisTime = time();
        $mine = $thisTime - $time;
        if ($mine > 0) {
            if ($mine < 60) {
                return "$mine ثانیه پیش";
            } elseif ($mine < 3600) {
                return round($mine / 60).' دقیقه پیش';
            } elseif ($mine < 86400) {
                return round($mine / 3600).' ساعت پیش';
            } elseif ($mine < 604800) {
                return round($mine / 86400).' روز پیش';
            } elseif ($mine < 2592000) {
                return round($mine / 604800).' هفته پیش';
            } else {
                return round($mine / 2592000).' ماه پیش';
            }
        } else {
            $mine = $time - $thisTime;
            if (0 == $mine) {
                return 'همین الان';
            } elseif ($mine < 60) {
                return "$mine ثانیه بعد";
            } elseif ($mine < 3600) {
                return round($mine / 60).' دقیقه بعد';
            } elseif ($mine < 86400) {
                return round($mine / 3600).' ساعت بعد';
            } elseif ($mine < 604800) {
                return round($mine / 86400).' روز بعد';
            } elseif ($mine < 2592000) {
                return round($mine / 604800).' هفته بعد';
            } else {
                return round($mine / 2592000).' ماه بعد';
            }
        }
    }
}
