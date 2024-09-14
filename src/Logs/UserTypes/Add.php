<?php

namespace packages\userpanel\Logs\UserTypes;

use packages\base\{View};
use packages\userpanel\Logs;
use packages\userpanel\Logs\Panel;

class Add extends Logs
{
    use UserTypeTrait;

    public function getColor(): string
    {
        return 'circle-success';
    }

    public function getIcon(): string
    {
        return 'fa fa-plus';
    }

    public function buildFrontend(View $view)
    {
        $view->addBodyClass('usertypes-add-logs');

        $parameters = $this->log->parameters;

        $newData = $parameters['new'] ?? [];

        if ($newData) {
            $html = $this->getHTML($newData);

            if ($html) {
                $panel = new Panel('userpanel.usertypes.add');
                $panel->icon = 'fa fa-plus';
                $panel->size = 6;
                $panel->title = t('userpanel.usertypes.logs.add_data');

                $panel->setHTML($html);
                $this->addPanel($panel);
            }
        }
    }
}
