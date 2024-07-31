// tslint:disable-next-line: no-reference
/// <reference path="../definitions/jquery.growl.d.ts" />

import "@jalno/translator";
import "jquery.growl";
import ViewError from "../definitions/ViewError";
import {View} from "../pages/Main";
import "./jquery.formAjax";
import {Main} from "./Main";
import Country, { ICountryCode } from "./Country";
import IFormAjaxError from "./IFormAjaxError";
import { Router } from "webuilder";

declare const countriesCode: ICountryCode[];
declare const defaultCountryCode: string;

export class Resetpwd {

	public static initIfNeeded(): void {
		Resetpwd.$body = $("body.resetpwd, body.newpwd");
		if (Resetpwd.$body.length) {
			Resetpwd.init();
		}
	}

	protected static init(): void {
		if (Resetpwd.$body.hasClass("resetpwd")) {
			Resetpwd.$resetPasswordForm = $("form.form-resetpwd", Resetpwd.$body);
			Resetpwd.$authenticationForm = $("form.form-authentication", Resetpwd.$body);
			Main.SetDefaultValidation();
			Resetpwd.runResetPasswordFormListener();
			Resetpwd.runTokenFormSubmitListener();
			Resetpwd.runSelect2();
			Resetpwd.credentialChangeListener();
		} else if (Resetpwd.$body.hasClass("newpwd")) {
			Resetpwd.$newPasswordForm = $(".form-changepwd", Resetpwd.$body);
			Resetpwd.runNewPasswordFormSubmitListener();
		}
	}

	private static $body: JQuery;
	private static $resetPasswordForm: JQuery;
	private static $authenticationForm: JQuery;
	private static $newPasswordForm: JQuery;
	private static credential: string | {
		number: string;
		code: string;
	} = null;

	private static credentialChangeListener() {
		const isNumeric = (value: string): boolean => {
			return /^-?\d+$/.test(value);
		}
		$("input[name=credential]", Resetpwd.$resetPasswordForm).on("change keyup input", function(e) {
			const value = $(this).val();
			const $code = $('select[name="credential[code]"]', $(this).parents(".input-group"));
			if (isNumeric(value)) {
				$code.parent().removeClass("hidden");
				Resetpwd.credential = {
					code: $code.val(),
					number: value,
				};
			} else {
				$code.parent().addClass("hidden");
				Resetpwd.credential = value;
			}
		}).trigger("change");
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

	private static runResetPasswordFormListener(): void {
		Resetpwd.$resetPasswordForm.on("submit", (e) => {
			e.preventDefault();
			const method = $("input[name=method]:checked").val();
			if (!method) {
				return;
			}
			Resetpwd.$resetPasswordForm.formAjax({
				data: {
					credential: Resetpwd.credential,
					method: method,
				},
				method: "POST",
				success: (data) => {
					Resetpwd.$resetPasswordForm.hide();
					switch (method) {
						case ("sms"):
							$("input[name=credential]", Resetpwd.$authenticationForm).prop("type", "hidden");
							Resetpwd.$authenticationForm.show();
							break;
						case ("email"):
							$(".box-forgot .email-alert", Resetpwd.$body).show();
							break;
					}
				},
				error: (error: IFormAjaxError) => {
					if (error.error === "data_duplicate" || error.error === "data_validation") {
						const $viewError = new ViewError();
						$viewError.setType(error.type);
						$viewError.setCode(error.code);
						$viewError.setMessage(t("userpanel.data_validation.resetpwd.emailorcellphone"));
						const view = new View();
						view.addError($viewError);
						view.getErrorHTML();
					} else {
						if (error.hasOwnProperty("code")) {
							const $viewError = new ViewError();
							$viewError.setType(error.type);
							$viewError.setCode(error.code);
							$viewError.setMessage(error.message);
							$viewError.setData(error.data);
							const view = new View();
							view.addError($viewError);
							view.getErrorHTML();
						}
					}
				},
			});
		});
		
	}

	private static runTokenFormSubmitListener() {
		Resetpwd.$authenticationForm.on("submit", function(e) {
			e.preventDefault();
			const $token = $("input[name=token]", Resetpwd.$authenticationForm);
			$(this).formAjax({
				data: {
					credential: Resetpwd.credential,
					token: $token.val(),
				},
				success: (data) => {
					window.location.href = data.redirect;
				},
				error: (error: IFormAjaxError) => {
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
		Resetpwd.$newPasswordForm.on("submit", function(e) {
			e.preventDefault();
			$(this).formAjax({
				success: (data) => {
					$.growl.notice({
						title: t("userpanel.success"),
						message: t("userpanel.formajax.success"),
					});
					const redirect = data.redirect ? data.redirect : Router.url("userpanel");
					setTimeout(() => {
						window.location.href = redirect;
					}, 2000);
				},
				error: (error: IFormAjaxError) => {
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
					} else {
						if (error.hasOwnProperty("code")) {
							const $viewError = new ViewError();
							$viewError.setType(error.type);
							$viewError.setCode(error.code);
							$viewError.setMessage(error.message);
							$viewError.setData(error.data);
							const view = new View();
							view.addError($viewError);
							view.getErrorHTML();
						}
					}
				},
			});
		});
	}
}
