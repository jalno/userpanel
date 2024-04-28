<?php

namespace packages\userpanel\Logs\UserTypes;

use packages\base\{View};
use packages\userpanel\Logs;
use packages\userpanel\Logs\Panel;

class Delete extends Logs
{
    use UserTypeTrait;

    public function getColor(): string
    {
        return 'circle-bricky';
    }

    public function getIcon(): string
    {
        return 'fa fa-trash';
    }

    public function buildFrontend(View $view)
    {
        $view->addBodyClass('usertypes-delete-logs');

        $parameters = $this->log->parameters;

        $oldData = $parameters['old'] ?? [];

        if ($oldData) {
            $html = $this->getHTML($oldData);

            if ($html) {
                $panel = new Panel('userpanel.usertypes.old');
                $panel->icon = 'fa fa-trash';
                $panel->size = 6;
                $panel->title = t('userpanel.usertypes.logs.old_data');

                $panel->setHTML($html);
                $this->addPanel($panel);
            }
        }
    }
}
