import "@jalno/translator";
import * as $ from "jquery";
import "jquery-validation";
import { webuilder } from "webuilder";
import "./jquery.formAjax";
import {Main} from "./Main";

export class Login {
	public static init(): void {
		Main.SetDefaultValidation();
		Login.runLoginButtons();
		Login.runLoginValidator();
	}
	public static initIfNeeded(): void {
		if ($("body").hasClass("login")) {
			Login.init();
		}
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
				$errorHandler.hide();
				$(form).formAjax({
					success: (data: webuilder.AjaxResponse) => {
						window.location.href = data.redirect;
					},
					error: (response: webuilder.AjaxError) => {
						if (response.error === "user_status_is_deactive_in_login" || response.error === "user_status_is_suspend_in_login") {
							$errorHandler.html(t(`error.${response.error}`)).show();
						} else if (response.error === "data_validation") {
							$errorHandler.html(`<i class="fa fa-remove-sign"></i> ${t("userpanel.login.incorrect")}.`).show();
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
