<?php

namespace themes\clipone\Views;

use packages\base\Translator;
use packages\userpanel\Authentication;
use packages\userpanel\Views\Forbidden as ForbiddenView;
use themes\clipone\ViewTrait;

class Forbidden extends ForbiddenView
{
    use ViewTrait;
    protected $loged_in;

    public function __beforeLoad()
    {
        $this->setTitle(Translator::trans('forbidden'));
        $this->loged_in = Authentication::check();

        if (!$this->loged_in) {
            $this->addBodyClass('error-full-page');
        }
    }
}
