<?php

namespace packages\userpanel\Search;

abstract class Result
{
    abstract public function getLink();

    abstract public function getTitle();

    abstract public function getDescription();
}
