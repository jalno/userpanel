<?php

namespace themes\clipone\Users;

use packages\userpanel\Country;
use packages\userpanel\Register\RegisterField;
use packages\userpanel\Register\RegisterFields;

trait RegisterTrait
{
    /**
     * @return array{has_more_non_credientials:bool,is_crediential:bool,value:RegisterField,field:array<string,mixed>}[]
     */
    public function getRegisterFields(): array
    {
        $registerFields = RegisterFields::actives(true);
        $totalCount = count($registerFields);
        $credientialsCount = count(RegisterFields::credientials());
        $hasMoreNonCredientials = $totalCount - $credientialsCount > 1;

        return array_map(
            function (RegisterField $field) use ($hasMoreNonCredientials): array {
                switch ($field) {
                    case RegisterField::NAME:
                    case RegisterField::LASTNAME:
                    case RegisterField::ADDRESS:
                    case RegisterField::CITY:
                    case RegisterField::ZIP:
                        return [
                            'has_more_non_credientials' => $hasMoreNonCredientials,
                            'is_crediential' => false,
                            'value' => $field,
                            'field' => [
                                'name' => $field->value,
                                'required' => $field->isRequired(),
                                'label' => t("register.user.{$field->value}"),
                                'placeholder' => t("register.user.{$field->value}"),
                            ],
                        ];
                    case RegisterField::COUNTRY:
                        return [
                            'has_more_non_credientials' => $hasMoreNonCredientials,
                            'is_crediential' => false,
                            'value' => $field,
                            'field' => [
                                'type' => 'select',
                                'name' => $field->value,
                                'required' => $field->isRequired(),
                                'label' => t("register.user.{$field->value}") ?: $field->value,
                                'placeholder' => t("register.user.{$field->value}"),
                                'options' => $this->getCountriesForSelect(),
                            ],
                        ];
                    case RegisterField::PHONE:
                        return [
                            'has_more_non_credientials' => $hasMoreNonCredientials,
                            'is_crediential' => false,
                            'value' => $field,
                            'field' => [
                                'name' => "{$field->value}[number]",
                                'required' => $field->isRequired(),
                                'label' => t("register.user.{$field->value}"),
                                'placeholder' => t("register.user.{$field->value}"),
                                'input-group' => [
                                    'first' => [
                                        [
                                            'type' => 'select',
                                            'name' => "{$field->value}[code]",
                                            'options' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ];
                    case RegisterField::CELLPHONE:
                        return [
                            'has_more_non_credientials' => $hasMoreNonCredientials,
                            'is_crediential' => true,
                            'value' => $field,
                            'field' => [
                                'name' => "{$field->value}[number]",
                                'required' => $field->isRequired(),
                                'placeholder' => t("register.user.{$field->value}"),
                                'label' => t("register.user.{$field->value}"),
                                'input-group' => [
                                    'first' => [
                                        [
                                            'type' => 'select',
                                            'name' => "{$field->value}[code]",
                                            'options' => [],
                                        ],
                                    ],
                                ],
                            ],
                        ];
                        break;
                    case RegisterField::EMAIL:
                        return [
                            'has_more_non_credientials' => $hasMoreNonCredientials,
                            'is_crediential' => true,
                            'value' => $field,
                            'field' => [
                                'name' => $field->value,
                                'required' => $field->isRequired(),
                                'type' => 'email',
                                'icon' => 'fa fa-envelope',
                                'label' => t("register.user.{$field->value}"),
                                'placeholder' => t("register.user.{$field->value}"),
                            ],
                        ];
                        break;
                }
            },
            $registerFields
        );
    }

    public function getCountriesForSelect(): array
    {
        return array_map(function ($country) {
            return [
                'title' => $country->name,
                'value' => $country->id,
            ];
        }, Country::get());
    }
}
