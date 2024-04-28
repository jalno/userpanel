<?php

namespace themes\clipone\Views;

use packages\base;
use packages\base\Events;
use packages\base\Frontend\Theme;
use packages\base\Packages;
use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Authentication;
use packages\userpanel\Authorization;
use packages\userpanel\Date;
use packages\userpanel\User;
use packages\userpanel\Views\Dashboard as DashboardView;
use themes\clipone\Events\InitializeDashboard;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\Views\Dashboard\Box;
use themes\clipone\Views\Dashboard\Panel;
use themes\clipone\Views\Dashboard\Shortcut;
use themes\clipone\ViewTrait;

class Dashboard extends DashboardView
{
    use ViewTrait;
    public static $shortcuts = [];
    public static $boxs = [];

    public static function getSettingsMenu()
    {
        if (!self::$settingsMenuItem) {
            $item = new MenuItem('settings');
            $item->setTitle(Translator::trans('settings'));
            $item->setIcon('clip-settings');
            self::$settingsMenuItem = $item;
        }

        return self::$settingsMenuItem;
    }
    protected static $settingsMenuItem;

    public function __beforeLoad()
    {
        $this->setTitle(Translator::trans('dashboard'));
        Navigation::active('dashboard');
        $initEvent = new InitializeDashboard();
        $initEvent->view = $this;
        Events::trigger($initEvent);
        $types = Authorization::childrenTypes();
        if (Authorization::is_accessed('users_list') and $types) {
            $today = Date::mktime(0, 0, 0);
            $user = new User();
            $user->where('type', $types, 'in');
            $user->where('id', Authentication::getID(), '!=');
            $user->where('lastonline', $today, '>=');
            $users = $user->count();
            $shortcut = new Shortcut('users');
            $shortcut->icon = 'fa fa-users';
            if ($users) {
                $shortcut->title = $users;
                $shortcut->text = Translator::trans('shortcut.users.loggined');
            } else {
                $shortcut->text = Translator::trans('shortcut.users.loggined.iszero');
            }
            $shortcut->setLink(Translator::trans('shortcut.users.link'), userpanel\url('users', ['lastonline_from' => Date::format('Y/m/d H:i', $today)]));
            self::addShortcut($shortcut);
            self::addBox($this->createOnlineUsers());
        }
    }

    public static function addShortcut(Shortcut $shortcut)
    {
        foreach (self::$shortcuts as $key => $item) {
            if ($item->name == $shortcut->name) {
                self::$shortcuts[$key] = $shortcut;

                return;
            }
        }
        self::$shortcuts[] = $shortcut;
    }

    public static function addBox(Box $box)
    {
        self::$boxs[] = $box;
    }

    public function getBoxs()
    {
        return self::$boxs;
    }

    public function generateShortcuts()
    {
        $rows = [];
        $lastrow = 0;
        usort(self::$shortcuts, function ($a, $b) {
            return $b->priority - $a->priority;
        });
        foreach (self::$shortcuts as $box) {
            $rows[$lastrow][] = $box;
            $size = 0;
            foreach ($rows[$lastrow] as $rowbox) {
                $size += $rowbox->size;
            }
            if ($size >= 12) {
                ++$lastrow;
            }
        }
        $html = '';
        foreach ($rows as $row) {
            $html .= '<div class="row">';
            foreach ($row as $shortcut) {
                $html .= "<div class=\"col-sm-{$shortcut->size}\">";
                $html .= '<div class="core-box '.($shortcut->color ? "box-{$shortcut->color}" : '').'">';
                $html .= '<div class="heading">';
                $html .= "<i class=\"{$shortcut->icon}\"></i>";
                $html .= "<h2>{$shortcut->title}</h2>";
                if ($shortcut->description) {
                    $html .= "<p class=\"box-description\">{$shortcut->description}</p>";
                }
                $html .= '</div>';
                if ($shortcut->text) {
                    $html .= "<div class=\"content\">{$shortcut->text}</div>";
                }
                $iconDirection = ((bool) Translator::getLang()->isRTL()) ? 'left' : 'right';
                if (!empty($shortcut->link)) {
                    $html .= '<a class="view-more" href="'.$shortcut->link[1].'">';
                    $html .= '<i class="clip-arrow-'.$iconDirection.'-2"></i> '.$shortcut->link[0].'</a>';
                }
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }

        return $html;
    }

    public function generateRows()
    {
        $rows = [];
        $lastrow = 0;
        usort(self::$boxs, function ($a, $b) {
            return $b->priority - $a->priority;
        });
        foreach (self::$boxs as $box) {
            $rows[$lastrow][] = $box;
            $size = 0;
            foreach ($rows[$lastrow] as $rowbox) {
                $size += $rowbox->size;
            }
            if ($size >= 12) {
                ++$lastrow;
            }
        }
        $html = '';
        foreach ($rows as $row) {
            $html .= '<div class="row">';
            foreach ($row as $box) {
                $html .= "<div class=\"col-md-{$box->size}\">".$box->getHTML().'</div>';
            }
            $html .= '</div>';
        }

        return $html;
    }

    protected function createOnlineUsers()
    {
        $panel = new Panel('users_online');
        $panel->size = 5;
        $panel->title = Translator::trans('users.online');
        $panel->icon = 'fa fa-users';
        $panel->scroll = true;
        $panel->scrollHeight = 300;
        $html = '<table class="table table-condensed table-hover">';
        $html .= '<thead><tr>';
        $html .= '<th>'.Translator::trans('user.avatar').'</th>';
        $html .= '<th>'.Translator::trans('user.name').'</th>';
        $html .= '<th></th>';
        $html .= '</tr></thead>';
        $html .= '<tbody>';
        $types = Authorization::childrenTypes();
        $user = new User();
        if ($types) {
            $user->where('type', $types, 'in');
        } else {
            $user->where('id', Authentication::getID());
        }
        $user->where('lastonline', Date::time() - User::onlineTimeout, '>=');
        foreach ($user->get() as $user) {
            $html .= '<tr>';
            $html .= '<td><img src="'.$this->getAvatarURL($user)."\" class=\"img-responsive\" width=\"50\" height=\"50\" alt=\"User #{$user->id}\"></td>";
            $html .= '<td>'.$user->getFullName().'</td>';
            $html .= '<td>';
            if (Authorization::is_accessed('users_view')) {
                $html .= '<a href="'.userpanel\url('users/view/'.$user->id).'" class="btn btn-xs btn-green tooltips" title="'.Translator::trans('user.profile').'"><i class="fa fa-credit-card"></i></a>';
            }
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        $panel->setHTML($html);

        return $panel;
    }

    protected function getAvatarURL(User $user)
    {
        if ($user->avatar) {
            return Packages::package('userpanel')->url($user->avatar);
        } else {
            return Theme::url('assets/images/defaultavatar.jpg');
        }
    }

    public static function onSourceLoad()
    {
        $item = new MenuItem('dashboard');
        $item->setTitle(t('dashboard'));
        $item->setURL(base\url('userpanel'));
        $item->setIcon('clip-home-3');
        $item->setPriority(0);
        Navigation::addItem($item);
        Navigation::addItem(self::getSettingsMenu());
    }
}
