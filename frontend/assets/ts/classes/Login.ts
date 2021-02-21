import "@jalno/translator";
import * as $ from "jquery";
import "jquery-validation";
import { webuilder } from "webuilder";
import "./jquery.formAjax";
import "select2";
import {Main} from "./Main";
import Country, { ICountry } from "./Country";

declare const countries: ICountry[];
declare const defaultCountry: ICountry;

export class Login {
	public static init(): void {
		Main.SetDefaultValidation();
		Login.runLoginButtons();
		Login.runLoginValidator();
		Login.runSelect2();
		Login.loginCredentialChangeListener();
	}
	public static initIfNeeded(): void {
		if ($("body").hasClass("login")) {
			Login.init();
		}
	}
	private static loginCredentialChangeListener() {
		const isNumeric = (value: string): boolean => {
			return /^-?\d+$/.test(value);
		}
		const $dialingCodeContainer = $(".credential-container .input-group-btn");
		$("input[name=credential]").on("change keyup input", function(e) {
			const value = $(this).val();
			if (isNumeric(value)) {
				$dialingCodeContainer.removeClass("hidden");
			} else {
				$dialingCodeContainer.addClass("hidden");
			}
		});
	}
	private static runSelect2(): void {
		Country.runCountryDialingCodeSelect2($(`select[name="credential[code]"]`), countries.map((country) => {
			return {
				id: country.dialing_code,
				text: country.name + '-' + country.code,
				selected: country.id === defaultCountry.id,
			};
		}));
	}
	private static runLoginButtons(): void {
		$(".forgot").on("click", () => {
			$(".box-login").hide();
			$(".box-forgot").show();
		});
		$(".go-back").on("click", () => {
			$(".box-login").show();
			$(".box-forgot").hide();
			$(".box-register").hide();
		});
	}
	private static runLoginValidator(): void {
		const $form = $(".form-login");
		const $errorHandler = $(".errorHandler", $form);
		$errorHandler.data("orghtml", $errorHandler.html());
		$form.validate({
			rules: {
				username: {
					required: true,
				},
				password: {
					required: true,
				},
			},
			submitHandler: (form) => {
				const isNumeric = (value: string): boolean => {
					return /^-?\d+$/.test(value);
				}
				$errorHandler.hide();
				const $password = $("input[name=password]");
				const $credential = $(`input[name=credential]`);
				const $countryCode = $(`select[name="credential[code]"]`);
				$(form).formAjax({
					data: {
						credential: isNumeric($credential.val()) ? {
							number: $credential.val(),
							code: $countryCode.val(),
						} : $credential.val(),
						password: $password.val(),
					},
					success: (data: webuilder.AjaxResponse) => {
						window.location.href = data.redirect;
					},
					error: (response) => {
						const code = response.hasOwnProperty("error") ? response.error : response.code;
						if (code === "data_validation") {
							$errorHandler.html(`<i class="fa fa-remove-sign"></i> ${t("userpanel.login.incorrect")}.`).show();
						} else {
							let message;
							if (response.hasOwnProperty("message") && response.message) {
								message = response.message;
							} else {
								message = t(`error.${code}`);
							}
							if (!message) {
								message = t("userpanel.formajax.error");
							}
							$errorHandler.html(message).show();
						}
					},
				});
			},
			invalidHandler: (event, validator) => {
				$errorHandler.html($errorHandler.data("orghtml")).show();
			},
		});
	}
}
