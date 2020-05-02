// tslint:disable-next-line: no-reference
/// <reference path="../definitions/jquery.growl.d.ts" />

import "@jalno/translator";
import * as $ from "jquery";
import "jquery-validation";
import "jquery.growl";
import { webuilder } from "webuilder";
import "./jquery.formAjax";
import {Main} from "./Main";

export class Register {
	public static init(): void {
		Main.SetDefaultValidation();
		Register.runRegisterValidator();
	}

	public static initIfNeeded(): void {
		if ($("body").hasClass("register")) {
			Register.init();
		}
	}
	private static $form = $(".form-register");
	private static $errorHandler = $(".errorHandler", Register.$form);
	private static runRegisterValidator(): void {
		Main.importValidationTranslator();
		Register.$form.validate({
			rules: {
				name: {
					required: true,
				},
				lastname: {
					required: true,
				},
				country: {
					required: true,
				},
				city: {
					required: true,
				},
				address: {
					required: true,
				},
				zip: {
					required: true,
					digits: true,
					rangelength: [10, 10],
				},
				phone: {
					digits: true,
					required: true,
				},
				cellphone: {
					required: true,
					rangelength: [10, 13],
				},
				email: {
					required: true,
					email: true,
				},
				password: {
					required: true,
				},
				password_again: {
					equalTo: "input[name=password]",
				},
				tos: {
					required: true,
				},
			},
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
								const $input = $(`[name="${response.input}"]`);
								const params = {
									title: t("error.fatal.title"),
									message: "",
								};
								const code = response.hasOwnProperty("error") ? response.error : response.code;
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
