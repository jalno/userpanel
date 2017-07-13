/// <reference path="../definitions/jquery.growl.d.ts" />

import * as $ from "jquery";
import "jquery.growl";
import {Router} from "webuilder";
import {Main} from "./Main"
import "./jquery.formAjax";
import { webuilder } from "webuilder";

export class Register{
	private static form = $('.form-register');
	private static errorHandler = $('.errorHandler', Register.form);
	private static runRegisterValidator():void {
        Register.form.validate({
            rules: {
                name: {
                    required: true
                },
				lastname: {
                    required: true
                },
				country: {
                    required: true
                },
				city: {
                    required: true
                },
				address: {
                    required: true
                },
				zip: {
                    required: true,
					digits: true,
					rangelength: [10,10]
                },
				phone:{
      				digits: true,
					required: true,
				},
				cellphone:{
					required: true,
      				digits: true,
					rangelength:[10,12]
				},
				email: {
                    required: true,
					email:true
                },
				password: {
                    required: true,
                },
                password_again: {
					equalTo: 'input[name=password]'
                },
				tos:{
					required: true
				}
        	},
            submitHandler: (form) => {
				$(form).formAjax({
					success: function(data: webuilder.AjaxResponse){
						window.location.href = data.redirect;
					},
					error: function(error:webuilder.AjaxError){
						if(error.error == 'data_duplicate' || error.error == 'data_validation'){
							let $input = $('[name='+error.input+']');
							let $params = {
								title: 'خطا',
								message:''
							};
							if(error.error == 'data_duplicate'){
								if(error.input == 'email'){
									$params.message = 'این ایمیل متعلق به کاربر دیگری می باشد.';
								}else if(error.input == 'cellphone'){
									$params.message = 'این تلفن همراه متعلق به کاربر دیگری می باشد.';
								}
							}else if(error.error == 'data_validation'){
								$params.message = 'داده وارد شده معتبر نیست';
							}
							if($input.length){
								$input.inputMsg($params);
							}else{
								$.growl.error($params);
							}
						}else{
							Register.errorHandler.html('<i class="fa fa-remove-sign"></i> درخواست شما توسط سرور قبول نشد').show();
						}
					}
				});
			},
            invalidHandler: function (event, validator) {
                Register.errorHandler.html(Register.errorHandler.data('orghtml')).show();
            }
        });
    }
    public static init():void {
		Main.SetDefaultValidation();
		Register.runRegisterValidator();
    }

    public static initIfNeeded():void{
        if($('body').hasClass('register')){
            Register.init();
        }
    }
}