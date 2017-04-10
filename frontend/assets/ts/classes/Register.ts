/// <reference path="../definitions/jquery.growl.d.ts" />

import * as $ from "jquery";
import "jquery.growl";
import {webuilder, Router} from "webuilder";
import {Main} from "./Main"

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
            submitHandler: Register.sendAjaxRequest,
            invalidHandler: function (event, validator) {
                Register.errorHandler.html(Register.errorHandler.data('orghtml')).show();
            }
        });
    }
	private static sendAjaxRequest():void{
		let $btn = $('[type=submit]', Register.form);
		$btn.data('orghtml', $btn.html());
		$btn.prop('disabled', true);
		$btn.html('<i class="fa-li fa fa-spinner fa-spin"></i>');
		$.ajax({
			url:Router.getAjaxFormURL(Register.form.attr('action')),
			type:Register.form.attr('method'),
			data:Register.form.serialize(),
			dataType:'json',
			success:function(data:webuilder.AjaxResponse){
				$btn.prop('disabled', true);
				$btn.html($btn.data('orghtml'));

				if(data.status){
					window.location.href = data.redirect;
				}else{
					if(data.hasOwnProperty('error')){
						for(let i =0;i!=data.error.length;i++){
							let error = data.error[i];
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
						}

					}else{
						Register.errorHandler.html('<i class="fa fa-remove-sign"></i> درخواست شما توسط سرور قبول نشد').show();
					}
				}
			},
			error:function(){
				$btn.prop('disabled', true);
				$btn.html($btn.data('orghtml'));
				Register.errorHandler.html('<i class="fa fa-remove-sign"></i> اتصال به سرور ممکن نمیباشد').show();
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