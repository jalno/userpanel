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
					digits: true,
					rangelength: [10, 12],
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
					error: (response: webuilder.AjaxError) => {
						if (response.error === "user_status_is_deactive_in_register" || response.error === "user_status_is_suspend_in_register") {
							Register.$errorHandler.removeClass("alert-danger").addClass("alert-success").html(t(`error.${response.error}`)).show();
						} else if (response.error === "data_duplicate" || response.error === "data_validation") {
							const $input = $(`[name="${response.input}"]`);
							const params = {
								title: t("error.fatal.title"),
								message: "",
							};
							if (response.error === "data_duplicate") {
								params.message = t(`user.${response.input}.data_duplicate`);
							} else if (response.error === "data_validation") {
								params.message = t("data_validation");
							}
							if ($input.length) {
								$input.inputMsg(params);
							} else {
								$.growl.error(params);
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
