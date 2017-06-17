import * as $ from "jquery";
import "jquery-validation";
import {Main} from "./Main";
import { webuilder } from "webuilder";
import "./jquery.formAjax";

export class Login{
	private static runLoginButtons():void {
		$('.forgot').on('click', function () {
			$('.box-login').hide();
			$('.box-forgot').show();
		});
		$('.go-back').on('click',function () {
			$('.box-login').show();
			$('.box-forgot').hide();
			$('.box-register').hide();
		});
	}
	private static runLoginValidator():void {
		let form = $('.form-login');
		let errorHandler = $('.errorHandler', form);
		errorHandler.data('orghtml', errorHandler.html());
		form.validate({
			rules: {
				username: {
					required: true
				},
				password: {
					required: true
				}
			},
			submitHandler: function (form) {
				errorHandler.hide();
				$(form).formAjax({
					success: function(data:webuilder.AjaxResponse) {
						window.location.href = data.redirect;
					},
					error: function(error:webuilder.AjaxError){
						if(error.error == "data_validation"){
							errorHandler.html('<i class="fa fa-remove-sign"></i> نام کاربری یا کلمه عبور وارد شده صحیح نمیباشد.').show();
						}
					}
				})
			},
			invalidHandler: function (event, validator) {
				errorHandler.html(errorHandler.data('orghtml')).show();
			}
		});
	}
	public static init():void {
		Main.SetDefaultValidation();
		Login.runLoginButtons();
		Login.runLoginValidator();
	}
	public static initIfNeeded():void{
		if($('body').hasClass('login')){
			Login.init();
		}
	}
}
