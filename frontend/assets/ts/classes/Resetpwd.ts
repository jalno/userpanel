/// <reference path="../definitions/jquery.growl.d.ts" />

import * as $ from "jquery";
import "jquery.growl";
import {Main} from "./Main"
import "./jquery.formAjax";
import { webuilder } from "webuilder";
import viewError from "../definitions/viewError";
import {View} from "../pages/Main";
export class Resetpwd{
	private static form = $('.form-resetpwd');
	private static errorHandler = $('.errorHandler', Resetpwd.form);
	private static methodListener(){
		$('input[name=method]', Resetpwd.form).on('change', function(){
			if($(this).prop('checked')){
				switch($(this).val()){
					case('email'):
						$('input[name=username]').prop('type', 'email');
						break;
					case('sms'):
						$('input[name=username]').prop('type', 'text');
						break;
				}
			}
		}).trigger('change');
	}
	private static runResetpwdValidator():void {
        Resetpwd.form.validate({
            rules: {
				cellphone:{
					required: true,
      				digits: true,
					rangelength:[10,12]
				}
        	},
            submitHandler: (form) => {
				const method = $('input[name=method]:checked').val();
				$(form).formAjax({
					method: 'POST',
					success: function(data: webuilder.AjaxResponse){
						switch(method){
							case('sms'):
								Resetpwd.form.hide();
								Resetpwd.form = $('.form-authentication');
								Resetpwd.form.show();
								$('.cellphone', Resetpwd.form).html(data.username);
								$('input[name=username]', Resetpwd.form).val(data.username).prop('type', 'hidden');
								break;
							case('email'):
								$('.box-forgot .email-alert').show();
								break;
						}
					},
					error: function(error:webuilder.AjaxError){
						if(error.error == 'data_duplicate' || error.error == 'data_validation'){
							let $input = $('[name='+error.input+']');
							let $params = {
								title: 'خطا',
								message:''
							};
							if(error.error == 'data_validation'){
								if(error.input == 'username'){
									$params.message = 'کاربری با این مشخصه یافت نشد .';
								}else{
									$params.message = 'داده وارد شده معتبر نیست';
								}
							}
							if($input.length){
								$input.inputMsg($params);
							}else{
								$.growl.error($params);
							}
						}else{
							if(error.hasOwnProperty('code')){
								const $error:any = error;
								const $viewError = new viewError();
								$viewError.setType($error.setType);
								$viewError.setCode($error.code);
								$viewError.setMessage($error.message);
								$viewError.setData($error.data);
								const view = new View();
								view.addError($viewError);
								view.getErrorHTML();
							}
						}
					}
				});
			},
            invalidHandler: function (event, validator) {
                Resetpwd.errorHandler.html(Resetpwd.errorHandler.data('orghtml')).show();
            }
        });
	}
	private static runAuthenticationTokenFormSubmitListener(){
		$('.form-authentication').on('submit', function(e){
			e.preventDefault();
			$(this).formAjax({
				success: (data: webuilder.AjaxResponse) => {
					window.location.href = data.redirect;
				},
				error: function(error: webuilder.AjaxError){
					if(error.error == 'data_duplicate' || error.error == 'data_validation'){
						let $input:JQuery = $('[name='+error.input+']');
						let $params = {
							title: 'خطا',
							message:''
						};
						if(error.error == 'data_validation'){
							$params.message = 'داده وارد شده معتبر نیست';
						}
						if($input.length){
							$input.inputMsg($params);
						}else{
							$.growl.error($params);
						}
					}else{
						$.growl.error({
							title:"خطا",
							message:'درخواست شما توسط سرور قبول نشد'
						});
						if(error.hasOwnProperty('code')){
							const $error:any = error;
							const $viewError = new viewError();
							$viewError.setType($error.setType);
							$viewError.setCode($error.code);
							$viewError.setMessage($error.message);
							$viewError.setData($error.data);
							const view = new View();
							view.addError($viewError);
							view.getErrorHTML();
						}
					}
				}
			});
		});
	}
	private static runNewPasswordFormSubmitListener(){
		$('.form-changepwd').on('submit', function(e){
			e.preventDefault();
			$(this).formAjax({
				success: (data: webuilder.AjaxResponse) => {
					$.growl.notice({
						title:"موفق",
						message:"با موفقیت انجام شد ."
					});
					setTimeout(()=>{
						window.location.href = data.redirect;
					}, 2000);
				},
				error: function(error: webuilder.AjaxError){
					if(error.error == 'data_duplicate' || error.error == 'data_validation'){
						let $input:JQuery = $('[name='+error.input+']');
						let $params = {
							title: 'خطا',
							message:''
						};
						if(error.error == 'data_validation'){
							$params.message = 'داده وارد شده معتبر نیست';
						}
						if($input.length){
							$input.inputMsg($params);
						}else{
							$.growl.error($params);
						}
						if(error.input == 'dontmatch'){
							$('.form-changepwd .errorHandler').html('<i class="fa fa-remove-sign"></i> کلمه عبور و تکرار آن مطابقت ندارند .').show();
						}
					}else{
						$.growl.error({
							title:"خطا",
							message:'درخواست شما توسط سرور قبول نشد'
						});
					}
				}
			});
		});
	}
    public static init():void {
		const body = $('body');
		if(body.hasClass('resetpwd')){
			Resetpwd.methodListener();
			Main.SetDefaultValidation();
			Resetpwd.runResetpwdValidator();
			Resetpwd.runAuthenticationTokenFormSubmitListener();
		}else if(body.hasClass('newpwd')){
			Resetpwd.runNewPasswordFormSubmitListener();
		}
    }
    public static initIfNeeded():void{
		const body = $('body');
        if(body.hasClass('resetpwd') || body.hasClass('newpwd')){
            Resetpwd.init();
        }
    }
}