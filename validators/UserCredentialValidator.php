<?php
namespace packages\userpanel\validators;

use packages\base\{Validator\IValidator, Validator\CellphoneValidator, Validator\EmailValidator, InputValidationException};
use packages\userpanel\User;

class UserCredentialValidator implements IValidator {

	/**
	 * Get alias types
	 * 
	 * @return string[]
	 */
	public function getTypes(): array {
		return ["credential"];
	}

	/**
	 * @return User
	 */
	public function validate(string $input, array $rule, $data) {
		$isCellphone = false;
		$isEmail = false;
		try {
			$data = (new CellphoneValidator())->validate($input, array(
				'combined-output' => true,
			), $data);
			$isCellphone = true;
		} catch(InputValidationException $e) {}

		if (!$isCellphone) {
			try {
				(new EmailValidator())->validate($input, [], $data);
				$isEmail = true;
			} catch(InputValidationException $e) {}
		}

		if (!$isEmail and !$isCellphone) {
			throw new InputValidationException($input, "nor-email-cellphone");
		}

		$model = new User();
		if ($isCellphone) {
			$model->where("cellphone", $data);
		} else {
			$model->where("email", $data);
		}
		$user = $model->getOne();
		if (!$user) {
			throw new InputValidationException($input, "notfound");
		}
		return $user;
	}
}