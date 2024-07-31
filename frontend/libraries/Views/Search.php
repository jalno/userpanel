<?php

namespace themes\clipone\Views;

use packages\base\Translator;
use packages\userpanel;
use packages\userpanel\Views\Search as SearchView;
use themes\clipone\Breadcrumb;
use themes\clipone\Navigation;
use themes\clipone\Navigation\MenuItem;
use themes\clipone\ViewTrait;

class Search extends SearchView
{
    use ViewTrait;
    use ListTrait;
    use FormTrait;

    public function __beforeLoad()
    {
        $this->setTitle(t('search'));
        $this->setNavigation();
    }

    private function setNavigation()
    {
        $item = new MenuItem('search');
        $item->setTitle(t('search'));
        $item->setURL(userpanel\url('search'));
        $item->setIcon('clip-search');
        Breadcrumb::addItem($item);

        Navigation::active('dashboard');
    }
}
