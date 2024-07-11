<?php

namespace themes\clipone\Views\Users;

use packages\base\Frontend\Theme;
use packages\base\Options;
use packages\base\Packages;
use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Authorization;
use packages\userpanel\Log;
use packages\userpanel\User\SocialNetwork;
use packages\userpanel\Views\Users\View as UsersView;
use themes\clipone\Breadcrumb;
use themes\clipone\Events;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\BoxyTrait;
use themes\clipone\Views\Profile\ActivityCalendarBox;
use themes\clipone\Views\TabTrait;
use themes\clipone\Views\UserAdditionalInformationTrait;
use themes\clipone\ViewTrait;

class OverView extends UsersView
{
    use ViewTrait;
    use BoxyTrait;
    use TabTrait;
    use UserAdditionalInformationTrait;
    protected $networks = [];
    protected $lastlogin = 0;
    protected $lastIP;
    protected $logs = [];
    protected $canEdit = false;

    public function __beforeLoad()
    {
        $this->user = $this->getData('user');
        $this->setTitle(t('user.profile.overview'));
        $this->loadLastLog();
        $this->loadSocialnetworks();
        $this->setNavigation();
        $this->addBodyClass('users');
        $this->addBodyClass('users_view');
        $initEvent = new Events\InitializeProfile();
        $initEvent->view = $this;
        $initEvent->trigger();
        $this->addBox(new ActivityCalendarBox($this->user));
        $this->canEdit = Authorization::is_accessed('profile_edit');
    }

    private function loadLastLog(): void
    {
        $log = (new Log())
            ->where('user', $this->user->id)
            ->orderBy('time', 'DESC')
            ->getOne();
        if (!$log) {
            return;
        }
        $this->lastlogin = $log->time;
        $this->lastIP = $log->ip;
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
        $item = new MenuItem('users');
        $item->setTitle(Translator::trans('users'));
        $item->setURL(userpanel\url('users'));
        $item->setIcon('clip-users');
        Breadcrumb::addItem($item);

        $item = new MenuItem('user');
        $item->setTitle($this->getData('user')->getFullName());
        $item->setURL(userpanel\url('users/view/'.$this->getUserData('id')));
        $item->setIcon('clip-user');
        Breadcrumb::addItem($item);
        Navigation::active('users/list');
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
        if ($this->canViewInvisibles) {
            return true;
        }
        $user = $this->getData('user');

        return $user->getVisibility($field);
    }

    protected function getUserCurrency(): string
    {
        if (Packages::package('financial')) {
            return \packages\financial\Currency::getDefault($this->user)->title;
        } else {
            return Options::get('packages.userpanel.users.credit.currency.title');
        }
    }
}
