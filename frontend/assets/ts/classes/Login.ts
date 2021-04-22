import "@jalno/translator";
import * as $ from "jquery";
import "jquery-validation";
import { webuilder } from "webuilder";
import "./jquery.formAjax";
import "select2";
import {Main} from "./Main";
import Country, { ICountryCode } from "./Country";

declare const countriesCode: ICountryCode[];
declare const defaultCountryCode: string;

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
		const $form = $(".form-login");
		const $dialingCodeContainer = $(".credential-container .input-group-btn", $form);
		$("input[name=credential]", $form).on("change keyup input", function(e) {
			const value = $(this).val();
			if (isNumeric(value)) {
				$dialingCodeContainer.removeClass("hidden");
			} else {
				$dialingCodeContainer.addClass("hidden");
			}
		});
	}
	private static runSelect2(): void {
		Country.runCountryDialingCodeSelect2($(`select[name="credential[code]"]`), countriesCode.map((country) => {
			return {
				id: country.code,
				text: country.dialingCode + '-' + country.name,
				selected: country.code === defaultCountryCode,
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
		const isNumeric = (value: string): boolean => {
			return /^-?\d+$/.test(value);
		}
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
				$errorHandler.hide();

				const data: {[name: string]: string | {}} = {};

				const $countryCode = $(`select[name="credential[code]"]`);

				for (const item of $(form).serializeArray()) {
					if (item.name === "credential[code]") {
						continue;
					}
					let value: string | {} = item.value;
					if (item.name === "credential") {
						value = isNumeric(item.value) ? {
							number: item.value,
							code: $countryCode.val(),
						} : item.value
					}
					data[item.name] = value;
				}

				$(form).formAjax({
					data: data,
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
