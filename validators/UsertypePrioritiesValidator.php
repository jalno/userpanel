<?php
namespace packages\userpanel\validators;

use packages\base\{Validator\IValidator, InputValidationException};

class UsertypePrioritiesValidator implements IValidator {

	/**
	 * Get alias types
	 * 
	 * @return string[]
	 */
	public function getTypes(): array {
		return [];
	}

	/**
	 * @return int[]
	 */
	public function validate(string $input, array $rule, $data) {
		if(!is_array($data)) {
			throw new InputValidationException($input);
		}
		if (isset($rule['values'])) {
			foreach ($data as $key => $permission) {
				if(!in_array($permission, $rule['values'])){
					throw new InputValidationException("{$input}[{$key}]");
				}
			}
		}
		return array_values($data);
	}
}