<?php

namespace packages\userpanel\Register;

use packages\base\Options;

class RegisterFields
{
    public const DEACTIVE = 1;
    public const ACTIVE_REQUIRED = 2;
    public const ACTIVE_OPTIONAL = 3;

    protected const OPTION_NAME = 'packages.userpanel.register';

    /** @var array<string,int> */
    protected static ?array $attributes = null;

    protected const CREDIENTIAL_FIELDS = [RegisterField::CELLPHONE, RegisterField::EMAIL];

    /**
     * @return RegisterField[]
     */
    public static function all(bool $withCredientials = false): array
    {
        return $withCredientials ?
            RegisterField::cases() :
            array_filter(
                RegisterField::cases(),
                fn (RegisterField $f) => !in_array($f, self::CREDIENTIAL_FIELDS)
            );
    }

    /**
     * @return RegisterField[]
     */
    public static function credientials(): array
    {
        self::load();

        $credientals = array_filter(
            self::actives(),
            fn (RegisterField $f) => in_array($f, self::CREDIENTIAL_FIELDS)
        );
        if (!$credientals) {
            $credientals = self::CREDIENTIAL_FIELDS;
        }

        return $credientals;
    }

    /**
     * @return RegisterField[]
     */
    public static function actives(bool $withCredientials = false): array
    {
        self::load();

        if (self::$attributes) {
            return self::filterByStatus(
                $withCredientials ?
                    self::$attributes :
                    array_filter(
                        self::$attributes,
                        fn (string $field) => !in_array(RegisterField::from($field), self::CREDIENTIAL_FIELDS),
                        ARRAY_FILTER_USE_KEY
                    ),
                [self::ACTIVE_OPTIONAL, self::ACTIVE_REQUIRED]
            );
        }

        return self::all($withCredientials);
    }

    /**
     * @return RegisterField[]
     */
    public static function optionals(bool $withCredientials = false): array
    {
        self::load();

        if (self::$attributes) {
            return self::filterByStatus(
                $withCredientials ?
                    self::$attributes :
                    array_filter(
                        self::$attributes,
                        fn (string $field) => !in_array(RegisterField::from($field), self::CREDIENTIAL_FIELDS),
                        ARRAY_FILTER_USE_KEY
                    ),
                [self::ACTIVE_OPTIONAL]
            );
        }

        return [];
    }

    /**
     * @return RegisterField[]
     */
    public static function requireds(bool $withCredientials = false): array
    {
        self::load();

        if (self::$attributes) {
            return self::filterByStatus(
                $withCredientials ?
                    self::$attributes :
                    array_filter(
                        self::$attributes,
                        fn (string $field) => !in_array(RegisterField::from($field), self::CREDIENTIAL_FIELDS),
                        ARRAY_FILTER_USE_KEY
                    ),
                [self::ACTIVE_REQUIRED]
            );
        }

        return self::all($withCredientials);
    }

    /**
     * @param array<string,int> $attributes
     * @param int[]             $statuses
     *
     * @return RegisterField[]
     */
    public static function filterByStatus(array $attributes, array $statuses): array
    {
        return array_map(
            fn (string $field) => RegisterField::from($field),
            array_keys(array_filter(
                $attributes,
                fn (int $status) => in_array($status, $statuses),
            ))
        );
    }

    protected static function load(): void
    {
        if (is_null(self::$attributes)) {
            $config = Options::get(self::OPTION_NAME);
            self::$attributes = $config['register_fields'] ?? [];
        }
    }
}
