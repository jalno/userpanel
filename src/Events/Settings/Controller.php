<?php

namespace packages\userpanel\Events\Settings;

use packages\userpanel\User;

interface Controller
{
    /**
     * @return (array of Log)
     */
    public function store(array $inputs, User $user): array;
}
