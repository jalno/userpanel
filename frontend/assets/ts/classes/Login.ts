import * as $ from "jquery";
import "jquery-validation";
import {Main} from "./Main";

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
                let $btn = $('[type=submit]', form);
                $btn.data('orghtml', $btn.html());
                $btn.html('<i class="fa-li fa fa-spinner fa-spin"></i>');
                $btn.prop('disabled', true);
                $.ajax({
                	url:$(form).attr('action'),
                	type:$(form).attr('method'),
                	data:$(form).serialize(),
                	dataType:'json',
                	success:function(data){
                		$btn.html( $btn.data('orghtml'));
                		$btn.prop('disabled', false);
                		if(data.hasOwnProperty('status')){
                			if(data.status){
		            			if(data.hasOwnProperty('redirect')){
		            				window.location.href = data.redirect;
		            			}
		            		}else{
		            			if(data.hasOwnProperty('error')){

		            				switch(data.error){
		            					case('invalid'):errorHandler.html('<i class="fa fa-remove-sign"></i> نام کاربری یا کلمه عبور وارد شده صحیح نمیباشد.').show();break;
		            					case("internal"):errorHandler.html('<i class="fa fa-remove-sign"></i> خطای داخلی، کد '+data.code).show();break;
		            					default:alert('پاسخ سرور: '+data.error);break;
		            				}
		            			}else{
		            				errorHandler.html('<i class="fa fa-remove-sign"></i> درخواست شما توسط سرور قبول نشد').show();
		            			}
		            		}
		            	}else{
            				errorHandler.html('<i class="fa fa-remove-sign"></i> در حال حاضر سرور پاسخ درخواست شما را به درستی ارسال نمیکند.').show();
            			}
                	},
                	error:function(){
                		$btn.html( $btn.data('orghtml'));
                		$btn.prop('disabled', false);
                		errorHandler.html('<i class="fa fa-remove-sign"></i> اتصال به سرور ممکن نمیباشد').show();
                	}
                });
            },
            invalidHandler: function (event, validator) {
                errorHandler.html(errorHandler.data('orghtml')).show();
            }
        });
    }
    private static runForgotValidator():void {
        let form2 = $('.form-forgot');
        let errorHandler2 = $('.errorHandler', form2);
        form2.validate({
            rules: {
                email: {
                    required: true
                }
            },
            submitHandler: function (form) {
                errorHandler2.hide();
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                errorHandler2.show();
            }
        });
    };

    public static init():void {
		Main.SetDefaultValidation();
		Login.runLoginButtons();
		Login.runLoginValidator();
		Login.runForgotValidator();
	}
    public static initIfNeeded():void{
        if($('body').hasClass('login')){
            Login.init();
        }
    }
}
