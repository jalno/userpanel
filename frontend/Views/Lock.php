<?php

namespace themes\clipone\Views;

use packages\base\Frontend\Theme;
use packages\base\Packages;
use packages\userpanel\Views\Lock as ParentView;
use themes\clipone\ViewTrait;

class Lock extends ParentView
{
    use ViewTrait;
    use FormTrait;
    /**
     * @var \packages\userpanel\User
     */
    protected $user;

    public function __beforeLoad()
    {
        $this->setTitle(t('unlock'));
        $this->user = $this->getUser();
    }

    protected function getSelfAvatarURL()
    {
        $user = $this->getUser();
        if ($user->avatar) {
            return Packages::package('userpanel')->url($user->avatar);
        }

        return Theme::url('assets/images/defaultavatar.jpg');
    }
}
