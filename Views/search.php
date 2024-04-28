<?php

namespace packages\userpanel\Views;

use packages\base\Views\Traits\Form as FormTrait;

class Search extends ListView
{
    use FormTrait;

    public function setResults($results)
    {
        $this->setDataList($results);
    }

    public function getResults()
    {
        return $this->getDataList();
    }

    public function setTotalResults($count)
    {
        $this->setData($count, 'totalResults');
    }

    public function getTotalResults()
    {
        return $this->totalItems;
    }

    public function export()
    {
        $export = parent::export();
        $export['data']['items'] = [];
        foreach ($this->getDataList() as $item) {
            $export['data']['items'][] = [
                'title' => $item->getTitle(),
                'link' => $item->getLink(),
                'description' => $item->getDescription(),
            ];
        }

        return $export;
    }
}
