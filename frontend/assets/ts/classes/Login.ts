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
		
		const errorMessage = Main.getUrlParameter("error_message");
		if(errorMessage){
			$errorHandler.html(errorMessage).show();
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
				$(form).formAjax({
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
