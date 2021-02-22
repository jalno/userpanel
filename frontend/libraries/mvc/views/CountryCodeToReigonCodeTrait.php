<?php
namespace themes\clipone\views;

use packages\base\{Validator\Geo\CountryCodeToRegionCodeMap, Options};

trait CountryCodeToReigonCodeTrait {
	public function generateCountiesArray(): array {
		$countries = array();
		foreach (CountryCodeToRegionCodeMap::$CC2RMap as $dialingCode => $relatedCountries) {
			foreach ($relatedCountries as $countryCode) {
				$countries[] = array(
					'name' => t("countries.code.iso_3166_1_alpha2.{$countryCode}"),
					'code' => $countryCode,
					'dialingCode' => strval($dialingCode),
				);
			}
		}
		usort($countries, function ($a, $b) {
			return strcmp($a['code'], $b['code']);
		});
		return $countries;
	}
	public function getDefaultCountryCode(): string {
		$countryCode = strval(Options::get("packages.base.validators.default_cellphone_country_code"));
		return $countryCode ?: "IR";
	}
}