var Login = function () {
    var runLoginButtons = function () {
        $('.forgot').bind('click', function () {
            $('.box-login').hide();
            $('.box-forgot').show();
        });
        $('.go-back').click(function () {
            $('.box-login').show();
            $('.box-forgot').hide();
            $('.box-register').hide();
        });
    };

    var runLoginValidator = function () {
        var form = $('.form-login');
        var errorHandler = $('.errorHandler', form);
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
                var $btn = $('[type=submit]', form);
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
    };
    var runForgotValidator = function () {
        var form2 = $('.form-forgot');
        var errorHandler2 = $('.errorHandler', form2);
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

    return {
        //main function to initiate template pages
        init: function () {
			Main.SetDefaultValidation();
            runLoginButtons();
            runLoginValidator();
            runForgotValidator();
        }
    };
}();
