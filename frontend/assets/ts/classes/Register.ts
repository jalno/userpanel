// tslint:disable-next-line: no-reference
/// <reference path="../definitions/jquery.growl.d.ts" />

import "@jalno/translator";
import * as $ from "jquery";
import "jquery-validation";
import "jquery.growl";
import { webuilder } from "webuilder";
import "./jquery.formAjax";
import {Main} from "./Main";
import Country, { ICountryCode } from "./Country";

declare const countriesCode: ICountryCode[];
declare const defaultCountryCode: string;

export class Register {
	public static init(): void {
		Main.SetDefaultValidation();
		Register.runRegisterValidator();
		Register.runSelect2();
	}

	public static initIfNeeded(): void {
		if ($("body").hasClass("register")) {
			Register.init();
		}
	}

	private static $form = $(".form-register");
	private static $errorHandler = $(".errorHandler", Register.$form);

	private static runSelect2(): void {
		const data = countriesCode.map((country) => {
			return {
				id: country.code,
				text: country.dialingCode + '-' + country.name,
				selected: country.code === defaultCountryCode,
			};
		});
		Country.runCountryDialingCodeSelect2($(`select[name="phone[code]"], select[name="cellphone[code]"]`), data);
	}
	private static runRegisterValidator(): void {
		Main.importValidationTranslator();
		const rules: JQueryValidation.RulesDictionary = {
			password: {
				required: true,
			},
			password_again: {
				equalTo: "input[name=password]",
			},
		};
		for (const input of ['name', 'lastname', 'country', 'city', 'address', 'zip', 'phone', 'cellphone', 'email', 'tos']) {
			const $input = $(`[name="${input}"]`, Register.$form);
			if ($input.length) {
				rules[input] = {
					required: !!$input.attr('required') || 'tos' === input,
				};
				if (['zip', 'phone'].indexOf(input) > -1) {
					rules[input].digits = true;
					if ('zip' === input) {
						rules[input].rangelength = [10, 10];
					}
				}
				else if ('cellphone' === input) {
					rules[input].rangelength = [10, 13];
				}
				else if ('email' === input) {
					rules[input].email = true;
				}
			}
		}
		Register.$form.validate({
			rules: rules,
			submitHandler: (form) => {
				Register.$errorHandler.removeClass("alert-success").addClass("alert-danger").hide();
				$(form).formAjax({
					success: (data: webuilder.AjaxResponse) => {
						window.location.href = data.redirect;
					},
					error: (response) => {
						if (response.hasOwnProperty("error") || response.hasOwnProperty("code")) {
							const code = response.hasOwnProperty("error") ? response.error : response.code;
							if (code === "data_duplicate" || code === "data_validation") {
								let inputName = response.input;
								if (inputName === "cellphone") {
									inputName = "cellphone[number]";
								} else if (inputName === "phone") {
									inputName = "phone[number]";
								}
								const $input = $(`[name="${inputName}"]`, form);
								const params = {
									title: t("error.fatal.title"),
									message: "",
								};
								if (code === "data_duplicate") {
									params.message = t(`user.${response.input}.data_duplicate`);
								} else if (code === "data_validation") {
									params.message = t("data_validation");
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
									Register.$errorHandler.html(`<i class="fa fa-times-circle"></i> ${message}`).show();
								}
								if ($input.length) {
									$input.inputMsg(params);
								} else {
									$.growl.error(params);
								}
							} else {
								let message = "";
								if (response.hasOwnProperty("message") && response.message) {
									message = response.message;
								} else {
									message = t(`error.${code}`);
								}
								if (!message) {
									message = t("userpanel.formajax.error");
								}
								Register.$errorHandler.removeClass("alert-danger").addClass("alert-success").html(message).show();
							}
						} else {
							Register.$errorHandler.html(`<i class="fa fa-times-circle"></i> ${t("userpanel.formajax.error")}`).show();
						}
					},
				});
			},
			invalidHandler: (event, validator) => {
				Register.$errorHandler.html(Register.$errorHandler.data("orghtml")).show();
			},
		});
	}
}
