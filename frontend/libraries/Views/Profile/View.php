<?php

namespace themes\clipone\Views\Profile;

use packages\base\Frontend\Theme;
use packages\base\Packages;
use packages\userpanel;
use packages\userpanel\Log;
use packages\userpanel\User\SocialNetwork;
use packages\userpanel\Views\Profile\View as profileView;
use themes\clipone\Breadcrumb;
use themes\clipone\Events;
use themes\clipone\Navigation;
use themes\clipone\Views\BoxyTrait;
use themes\clipone\Views\TabTrait;
use themes\clipone\Views\UserAdditionalInformationTrait;
use themes\clipone\ViewTrait;

class View extends profileView
{
    use ViewTrait;
    use BoxyTrait;
    use TabTrait;
    use UserAdditionalInformationTrait;

    protected $networks = [];
    protected $lastlogin = 0;
    protected $logs = [];
    protected $user;

    public function __beforeLoad()
    {
        $this->user = $this->getData('user');
        $this->setTitle(t('profile.view'));
        $this->loadLastLogin();
        $this->loadSocialnetworks();
        $this->addBodyClass('profile');
        $this->addBodyClass('profile_view');
        $this->setNavigation();
        $initEvent = new Events\InitializeProfile();
        $initEvent->view = $this;
        $initEvent->trigger();
        $this->addBox(new ActivityCalendarBox($this->getData('user')));
    }

    private function loadLastLogin()
    {
        $log = new Log();
        $log->where('user', $this->getUserData('id'));
        $log->orderBy('time', 'DESC');
        $this->lastlogin = $log->getValue('userpanel_logs.time');
    }

    private function loadSocialnetworks()
    {
        $networks = $this->getUserData('socialnetworks');
        if ($networks) {
            foreach ($networks as $network) {
                if ($this->is_public('socialnetworks_'.$network->network)) {
                    $name = '';
                    switch ($network->network) {
                        case SocialNetwork::facebook:$name = 'facebook';
                            break;
                        case SocialNetwork::twitter:$name = 'twitter';
                            break;
                        case SocialNetwork::gplus:$name = 'google-plus';
                            break;
                        case SocialNetwork::instagram:$name = 'instagram';
                            break;
                        case SocialNetwork::telegram:$name = 'telegram';
                            break;
                        case SocialNetwork::skype:$name = 'skype';
                            break;
                    }
                    if ($name) {
                        $this->networks[$name] = $network->getURL();
                    }
                }
            }
        }
    }

    private function setNavigation()
    {
        $item = new Navigation\MenuItem('profile');
        $item->setTitle(t('profile.view'));
        $item->setURL(userpanel\url('profile/view'));
        $item->setIcon('clip-user');
        Breadcrumb::addItem($item);
        Navigation::active('dashboard');
    }

    protected function getAvatarURL()
    {
        if ($this->getUserData('avatar')) {
            return Packages::package('userpanel')->url($this->getUserData('avatar'));
        } else {
            return Theme::url('assets/images/defaultavatar.jpg');
        }
    }

    protected function is_public($field)
    {
        $user = $this->getData('user');

        return $user->getVisibility($field);
    }
}
