// tslint:disable-next-line: no-reference
/// <reference path="../definitions/jquery.growl.d.ts" />

import "@jalno/translator";
import * as $ from "jquery";
import "jquery.growl";
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
			Main.SetDefaultValidation();
			Resetpwd.runResetpwdValidator();
			Resetpwd.runTokenFormSubmitListener();
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

	private static loginCredentialChangeListener() {
		const isNumeric = (value: string): boolean => {
			return /^-?\d+$/.test(value);
		}
		$("input[name=credential]").on("change keyup input", function(e) {
			const value = $(this).val();
			const $codeContainer = $('select[name="credential[code]"]', $(this).parents(".input-group")).parent();
			console.log("$codeContainer", $codeContainer)
			if (isNumeric(value)) {
				$codeContainer.removeClass("hidden");
			} else {
				$codeContainer.addClass("hidden");
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
	private static runResetpwdValidator(): void {
		Main.importValidationTranslator();
		const isNumeric = (value: string): boolean => {
			return /^-?\d+$/.test(value);
		}
		$(".form-resetpwd").on("submit", (e) => {
			e.preventDefault();
			const method = $("input[name=method]:checked").val();
			if (!method) {
				return;
			}
			const $credential = $(`input[name=credential]`);
			const $countryCode = $(`select[name="credential[code]"]`);
			const credential = isNumeric($credential.val()) ? `${$countryCode.val()}.${$credential.val()}` : $credential.val();
			$(".form-resetpwd").formAjax({
				data: {
					credential: credential,
					method: method,
				},
				method: "POST",
				success: (data) => {
					Resetpwd.form.hide();
					Resetpwd.form = $(".form-authentication");
					Resetpwd.form.show();
					$("input[name=credential]", Resetpwd.form).val(credential).prop("type", "hidden");

					switch (method) {
						case ("sms"):
							Resetpwd.form.hide();
							Resetpwd.form = $(".form-authentication");
							Resetpwd.form.show();
							$(".cellphone", Resetpwd.form).html(data.credential);
							$("input[name=credential]", Resetpwd.form).val(data.credential).prop("type", "hidden");
							break;
						case ("email"):
							$(".box-forgot .email-alert").show();
							break;
					}
				},
				error: (error: any) => {
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
		$(".form-authentication").on("submit", function(e) {
			e.preventDefault();
			$(this).formAjax({
				success: (data) => {
					window.location.href = data.redirect;
				},
				error: (error) => {
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
				success: (data) => {
					$.growl.notice({
						title: t("userpanel.success"),
						message: t("userpanel.formajax.success"),
					});
					setTimeout(() => {
						window.location.href = data.redirect;
					}, 2000);
				},
				error: (error: any) => {
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
