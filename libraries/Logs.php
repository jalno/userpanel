<?php

namespace packages\userpanel;

use packages\base\View;
use packages\userpanel\Logs\Panel;

abstract class Logs
{
    public $log;
    public static $panels = [];

    public function setLog(Log $log)
    {
        $this->log = $log;
    }

    abstract public function buildFrontend(View $view);

    abstract public function getColor(): string;

    abstract public function getIcon(): string;

    public static function addPanel(Panel $panel)
    {
        self::$panels[] = $panel;
    }

    public function getPanels()
    {
        return self::$panels;
    }

    public function generateRows()
    {
        $rows = [];
        $lastrow = 0;
        foreach ($this->getPanels() as $panel) {
            $rows[$lastrow][] = $panel;
            $size = 0;
            foreach ($rows[$lastrow] as $rowpanel) {
                $size += $rowpanel->size;
            }
            if ($size >= 12) {
                ++$lastrow;
            }
        }
        $html = '';
        foreach ($rows as $row) {
            $html .= '<div class="row">';
            foreach ($row as $panel) {
                $html .= "<div class=\"col-sm-{$panel->size}\">".$panel->getHTML().'</div>';
            }
            $html .= '</div>';
        }

        return $html;
    }
}
