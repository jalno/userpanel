<?php
namespace themes\clipone\listeners;

use packages\base\View;
use packages\userpanel;
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

		$tab = new Tab("view", views\Profile\View::class);
		$tab->setTitle("مشاهده پروفایل");
		$tab->setLink(userpanel\url("profile/view"));
		$view->addTab($tab);

		$tab = new Tab("edit", views\Profile\Edit::class);
		$tab->setTitle("ویرایش پروفایل");
		$tab->setLink(userpanel\url("profile/edit"));
		$view->addTab($tab);

		$tab = new Tab("settings", views\Profile\Settings::class);
		$tab->setTitle("تنظیمات کاربری");
		$tab->setLink(userpanel\url("profile/settings"));
		$view->addTab($tab);
	}
	private function addToUserAdmin(View $view) {
		$user = $view->getData('user')->id;

		$tab = new Tab("view", views\users\Overview::class);
		$tab->setTitle("مرور");
		$tab->setLink(userpanel\url("users/view/" . $user));
		$view->addTab($tab);

		$tab = new Tab("edit", views\users\Edit::class);
		$tab->setTitle("ویرایش اطلاعات");
		$tab->setLink(userpanel\url("users/edit/" . $user));
		$view->addTab($tab);

		$tab = new Tab("settings", views\users\Settings::class);
		$tab->setTitle("تنظیمات کاربری");
		$tab->setLink(userpanel\url("users/settings/" . $user));
		$view->addTab($tab);
	}
}