<?php

namespace packages\userpanel;

use packages\base\Events;
use packages\userpanel\Events\Search as SearchEvent;
use packages\userpanel\Search\Result;

class Search
{
    protected static $results = [];
    public static $totalCount = 0;
    public static $ipp = 25;

    public static function addResult(Result $result)
    {
        self::$results[] = $result;
    }

    public static function find($word)
    {
        Events::trigger(new SearchEvent($word));
        self::$totalCount = count(self::$results);

        return self::$results;
    }

    public static function paginate($word, $page)
    {
        Events::trigger(new SearchEvent($word));
        self::$totalCount = count(self::$results);

        return array_slice(self::$results, self::$ipp * ($page - 1), self::$ipp);
    }
}
