// tslint:disable-next-line: no-reference
/// <reference path="../definitions/jquery.growl.d.ts" />

import "@jalno/translator";
import * as $ from "jquery";
import "jquery.growl";
import { webuilder } from "webuilder";
import ViewError from "../definitions/ViewError";
import {View} from "../pages/Main";
import "./jquery.formAjax";
import {Main} from "./Main";
import Country, { ICountryCode } from "./Country";

declare const countriesCode: ICountryCode[];
declare const defaultCountryCode: string;

export class Resetpwd {
	public static init(): void {
		const body = $("body");
		if (body.hasClass("resetpwd")) {
			Resetpwd.methodListener();
			Main.SetDefaultValidation();
			Resetpwd.runResetpwdValidator();
			Resetpwd.runAuthenticationTokenFormSubmitListener();
			Resetpwd.runSelect2();
			Resetpwd.loginCredentialChangeListener();
		} else if (body.hasClass("newpwd")) {
			Resetpwd.runNewPasswordFormSubmitListener();
		}
	}
	public static initIfNeeded(): void {
		const body = $("body");
		if (body.hasClass("resetpwd") || body.hasClass("newpwd")) {
			Resetpwd.init();
		}
	}

	private static form = $(".form-resetpwd");
	private static errorHandler = $(".errorHandler", Resetpwd.form);

	private static loginCredentialChangeListener() {
		const isNumeric = (value: string): boolean => {
			return /^-?\d+$/.test(value);
		}
		$("input[name=username]").on("change keyup input", function(e) {
			const value = $(this).val();
			const $codeContainer = $('select[name="username[code]"]', $(this).parents(".input-group")).parent();
			console.log("$codeContainer", $codeContainer)
			if (isNumeric(value)) {
				$codeContainer.removeClass("hidden");
			} else {
				$codeContainer.addClass("hidden");
			}
		}).trigger("change");
	}
	private static runSelect2(): void {
		Country.runCountryDialingCodeSelect2($(`select[name="username[code]"]`), countriesCode.map((country) => {
			return {
				id: country.code,
				text: country.dialingCode + '-' + country.name,
				selected: country.code === defaultCountryCode,
			};
		}));
	}
	private static methodListener() {
		$("input[name=method]", Resetpwd.form).on("change", function() {
			if ($(this).prop("checked")) {
				switch ($(this).val()) {
					case("email"):
						$("input[name=username]").prop("type", "email");
						break;
					case("sms"):
						$("input[name=username]").prop("type", "text");
						break;
				}
			}
		}).trigger("change");
	}
	private static runResetpwdValidator(): void {
		Main.importValidationTranslator();
		Resetpwd.form.validate({
			rules: {
				cellphone: {
					required: true,
					digits: true,
					rangelength: [10, 12],
				},
			},
			submitHandler: (form) => {
				const isNumeric = (value: string): boolean => {
					return /^-?\d+$/.test(value);
				}
				const method = $("input[name=method]:checked").val();
				const $username = $(`input[name=username]`);
				const $countryCode = $(`select[name="username[code]"]`);
				$(form).formAjax({
					data: {
						username: isNumeric($username.val()) ? {
							number: $username.val(),
							code: $countryCode.val(),
						} : $username.val(),
						method: method,
					},
					method: "POST",
					success: (data: webuilder.AjaxResponse) => {
						switch (method) {
							case("sms"):
								Resetpwd.form.hide();
								Resetpwd.form = $(".form-authentication");
								Resetpwd.form.show();
								$(".cellphone", Resetpwd.form).html(data.username);
								$("input[name=username]", Resetpwd.form).val(data.username).prop("type", "hidden");
								break;
							case("email"):
								$(".box-forgot .email-alert").show();
								break;
						}
					},
					error: (error: webuilder.AjaxError) => {
						if (error.error === "data_duplicate" || error.error === "data_validation") {
							const $input = $(`[name="${error.input}"]`);
							const $params = {
								title: t("error.fatal.title"),
								message: "",
							};
							if (error.error === "data_validation") {
								if (error.input === "username") {
									$params.message = t("userpanel.data_validation.username");
								} else {
									$params.message = t("data_validation");
								}
							}
							if ($input.length) {
								$input.inputMsg($params);
							} else {
								$.growl.error($params);
							}
						} else {
							if (error.hasOwnProperty("code")) {
								const $error: any = error;
								const $viewError = new ViewError();
								$viewError.setType($error.setType);
								$viewError.setCode($error.code);
								$viewError.setMessage($error.message);
								$viewError.setData($error.data);
								const view = new View();
								view.addError($viewError);
								view.getErrorHTML();
							}
						}
					},
				});
			},
			invalidHandler: (event, validator) => {
				Resetpwd.errorHandler.html(Resetpwd.errorHandler.data("orghtml")).show();
			},
		});
	}
	private static runAuthenticationTokenFormSubmitListener() {
		$(".form-authentication").on("submit", function(e) {
			e.preventDefault();
			$(this).formAjax({
				success: (data: webuilder.AjaxResponse) => {
					window.location.href = data.redirect;
				},
				error: (error: webuilder.AjaxError) => {
					if (error.error === "data_duplicate" || error.error === "data_validation") {
						const $input: JQuery = $(`[name="${error.input}"]`);
						const params = {
							title: t("error.fatal.title"),
							message: t(error.error),
						};
						if ($input.length) {
							$input.inputMsg(params);
						} else {
							$.growl.error(params);
						}
					} else {
						$.growl.error({
							title: t("error.fatal.title"),
							message: t("userpanel.formajax.error"),
						});
						if (error.hasOwnProperty("code")) {
							const $error: any = error;
							const $viewError = new ViewError();
							$viewError.setType($error.setType);
							$viewError.setCode($error.code);
							$viewError.setMessage($error.message);
							$viewError.setData($error.data);
							const view = new View();
							view.addError($viewError);
							view.getErrorHTML();
						}
					}
				},
			});
		});
	}
	private static runNewPasswordFormSubmitListener() {
		$(".form-changepwd").on("submit", function(e) {
			e.preventDefault();
			$(this).formAjax({
				success: (data: webuilder.AjaxResponse) => {
					$.growl.notice({
						title: t("userpanel.success"),
						message: t("userpanel.formajax.success"),
					});
					setTimeout(() => {
						window.location.href = data.redirect;
					}, 2000);
				},
				error: (error: webuilder.AjaxError) => {
					if (error.error === "data_duplicate" || error.error === "data_validation") {
						const $input: JQuery = $(`[name="${error.input}"]`);
						const $params = {
							title: t("error.fatal.title"),
							message: t(error.error),
						};
						if ($input.length) {
							$input.inputMsg($params);
						} else {
							$.growl.error($params);
						}
						if (error.input === "dontmatch") {
							$(".form-changepwd .errorHandler").html(`<i class="fa fa-remove-sign"></i> ${t("userpanel.data_validation.password_again")} .`).show();
						}
					} else {
						$.growl.error({
							title: t("error.fatal.title"),
							message: t("userpanel.formajax.error"),
						});
					}
				},
			});
		});
	}
}
