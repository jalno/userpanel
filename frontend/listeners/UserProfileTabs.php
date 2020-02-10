<?php
namespace themes\clipone\listeners;

use packages\base\View;
use packages\userpanel;
use packages\userpanel\{Authorization};
use themes\clipone\{events, views, Tab};

class UserProfileTabs {
	public function handle(events\InitTabsEvent $event) {
		$view = $event->getView();
		if ($view instanceof views\Profile\Index) {
			$this->addToProfile($view);
		} elseif ($view instanceof views\Users\View) {
			$this->addToUserAdmin($view);
		}
	}
	private function addToProfile(View $view) {

		if (Authorization::is_accessed('profile_view')) {
			$tab = new Tab("view", views\Profile\View::class);
			$tab->setTitle(t("profile.view"));
			$tab->setLink(userpanel\url("profile/view"));
			$view->addTab($tab);
		}

		if (Authorization::is_accessed('profile_edit')) {
			$tab = new Tab("edit", views\Profile\Edit::class);
			$tab->setTitle(t("profile.edit"));
			$tab->setLink(userpanel\url("profile/edit"));
			$view->addTab($tab);
		}

		if (Authorization::is_accessed('profile_settings')) {
			$tab = new Tab("settings", views\Profile\Settings::class);
			$tab->setTitle(t("profile.settings"));
			$tab->setLink(userpanel\url("profile/settings"));
			$view->addTab($tab);
		}
	}
	private function addToUserAdmin(View $view) {
		$user = $view->getData('user')->id;

		if (Authorization::is_accessed('users_view')) {
			$tab = new Tab("view", views\users\Overview::class);
			$tab->setTitle(t("user.profile.overview"));
			$tab->setLink(userpanel\url("users/view/" . $user));
			$view->addTab($tab);
		}
		if (Authorization::is_accessed('users_edit')) {
			$tab = new Tab("edit", views\users\Edit::class);
			$tab->setTitle(t("profile.edit"));
			$tab->setLink(userpanel\url("users/edit/" . $user));
			$view->addTab($tab);
		}
		if (Authorization::is_accessed('users_settings')) {
			$tab = new Tab("settings", views\users\Settings::class);
			$tab->setTitle(t("users.settings"));
			$tab->setLink(userpanel\url("users/settings/" . $user));
			$view->addTab($tab);
		}
	}
}