<?php

namespace themes\clipone\Listeners;

use packages\base\View;
use packages\userpanel\Authentication;
use packages\userpanel\Authorization;
use packages\userpanel\User;
use themes\clipone\Events\InitializeProfile;
use themes\clipone\Users\ActionButton;
use themes\clipone\Views\Users as Views;

use function packages\userpanel\url;

class Profile
{
    protected ?View $view = null;
    protected ?User $user = null;

    public function initialize(InitializeProfile $event)
    {
        $this->view = $event->view;
        $this->user = $this->view->getData('user');

        $this->addLoginInToUserActionButton();
        $this->addSuspendActiveUserActionButton();
    }

    protected function addLoginInToUserActionButton(): void
    {
        if (
            !($this->view instanceof Views\OverView)
            or !Authorization::is_accessed('users_login')
            or $this->user->id == Authentication::getID()
        ) {
            return;
        }

        $button = new ActionButton(t('userpanel.user.login'), ActionButton::INFO);
        $button->setIcon('fa fa-user-secret');
        $button->setView($this->view);
        $button->setSubmitModalData(url('loginasuser/'.$this->user->id), t('userpanel.user.login'), t('userpanel.user.login.confirm', ['user-name' => $this->user->getFullName()]));
        $this->view->AdditionalInformations()->add($button);
    }

    protected function addSuspendActiveUserActionButton(): void
    {
        if (
            !($this->view instanceof Views\OverView)
            or !Authorization::is_accessed('users_edit')
        ) {
            return;
        }

        $button = null;

        if (User::suspend == $this->user->status) {
            $button = new ActionButton(t('userpanel.user.activate'), ActionButton::SUCCESS);
            $button->addClass('btn-active-user');
            $button->setIcon('fa fa-check-square');
        } else {
            $button = new ActionButton(t('userpanel.user.suspend'), ActionButton::WARNING);
            $button->addClass('btn-suspend-user');
            $button->setIcon('fa fa-user-times');
        }

        $button->addData('user', $this->user->id);
        $this->view->AdditionalInformations()->add($button);
    }
}
