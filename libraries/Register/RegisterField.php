<?php
namespace packages\userpanel\Register;

enum RegisterField: string
{
    case NAME = 'name';
    case LASTNAME = 'lastname';
    case EMAIL = 'email';
    case CELLPHONE = 'cellphone';
    case PHONE = 'phone';
    case COUNTRY = 'country';
    case CITY = 'city';
    case ADDRESS = 'address';
    case ZIP = 'zip';
    
    public function isDeactivated(): bool
    {
        return !in_array($this, RegisterFields::actives(true));
    }

    public function isRequired(): bool
    {
        return in_array($this, RegisterFields::requireds(true));
    }

    public function isOptional(): bool
    {
        return in_array($this, RegisterFields::actives(true));
    }
}