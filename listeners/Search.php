<?php

namespace packages\userpanel\listeners;

use packages\base\DB\Parenthesis;
use packages\base\Translator;
use packages\Userpanel;
use packages\userpanel\Authentication;
use packages\userpanel\Authorization;
use packages\userpanel\Date;
use packages\userpanel\Events\Search as Event;
use packages\userpanel\Search as SearchHandler;
use packages\userpanel\Search\Link;
use packages\userpanel\User;
use packages\userpanel\UserType;

class Search
{
    public function find(Event $e)
    {
        if (Authorization::is_accessed('users_list') and Authorization::is_accessed('users_view')) {
            $this->users($e->word);
        }
        if (Authorization::is_accessed('usertypes_list') and Authorization::is_accessed('usertypes_edit')) {
            $this->usertypes($e->word);
        }
        $this->profile($e->word);
    }

    public function users($word)
    {
        $types = Authorization::childrenTypes();
        $user = new User();
        if ($types) {
            $user->where('type', $types, 'in');
        } else {
            $user->where('id', Authentication::getID());
        }
        $parenthesis = new Parenthesis();
        foreach (['name', 'lastname', 'email', 'cellphone'] as $item) {
            $parenthesis->where($item, $word, 'contains', 'OR');
        }
        $user->where($parenthesis);
        $users = $user->get();
        foreach ($users as $user) {
            $result = new Link();
            $result->setLink(userpanel\Url('users/view/'.$user->id));
            $result->setTitle(Translator::trans('user.view.byFullName', [
                'fullname' => $user->getFullName(),
            ]));
            $result->setDescription(Translator::trans('search.user.description', [
                'email' => $user->email,
                'cellphone' => $user->cellphone,
                'lastonline' => Date::format('Q QT', $user->lastonline),
                'type_name' => $user->type->title,
            ]));
            SearchHandler::addResult($result);
        }
    }

    public function profile($word)
    {
        if (false !== strpos($word, Translator::trans('profile'))) {
            if (Authorization::is_accessed('profile_view')) {
                $result = new Link();
                $result->setLink(userpanel\Url('profile/view'));
                $result->setTitle(Translator::trans('profile.view'));
                SearchHandler::addResult($result);
            }
            if (Authorization::is_accessed('profile_edit')) {
                $result = new Link();
                $result->setLink(userpanel\url('profile/edit'));
                $result->setTitle(Translator::trans('profile.edit'));
                SearchHandler::addResult($result);
            }
        }
    }

    public function usertypes($word)
    {
        $types = Authorization::childrenTypes();
        $usertype = new UserType();
        $usertype->where('id', $types, 'in');
        $parenthesis = new Parenthesis();
        foreach (['title'] as $item) {
            $parenthesis->where($item, $word, 'contains', 'OR');
        }
        $usertype->where($parenthesis);
        $usertypes = $usertype->get();
        foreach ($usertypes as $usertype) {
            $result = new Link();
            $result->setLink(userpanel\Url('settings/usertypes/edit/'.$usertype->id));
            $result->setTitle(Translator::trans('usertype.edit.byTitle', [
                'title' => $usertype->title,
            ]));
            SearchHandler::addResult($result);
        }
    }
}
