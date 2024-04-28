<?php

namespace packages\userpanel\Events\General\Settings;

interface Controller
{
    /**
     * @return (array of Log)
     */
    public function store(array $inputs): array;
}
