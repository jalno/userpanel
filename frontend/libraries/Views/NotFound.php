<?php

namespace themes\clipone\Views;

use packages\base\Translator;
use packages\userpanel\Authentication;
use packages\userpanel\Views\NotFound as NotFoundView;
use themes\clipone\ViewTrait;

class NotFound extends NotFoundView
{
    use ViewTrait;
    protected $loged_in;

    public function __beforeLoad()
    {
        $this->setTitle(t('notfound'));
        $this->loged_in = Authentication::check();

        if (!$this->loged_in) {
            $this->addBodyClass('error-full-page');
        }
    }
}
