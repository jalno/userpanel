<?php

namespace themes\clipone\Views\ResetPWD;

use packages\userpanel\Authentication;
use packages\userpanel\Views\ResetPWD\NewPWD as NewPWDView;
use themes\clipone\Views\FormTrait;
use themes\clipone\ViewTrait;

class NewPWD extends NewPWDView
{
    use ViewTrait;
    use FormTrait;
    protected $user;

    public function __beforeLoad()
    {
        $this->user = Authentication::getUser();
        $this->setTitle('userpanel.resetpwd.set_new_password');
        $this->addBodyClass('newpwd');
    }
}
