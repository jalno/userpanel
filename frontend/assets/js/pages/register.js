var Register = function () {
	var errorHandler = $('.form-register .errorHandler');
	var runRegisterValidator = function () {
        var form = $('.form-register');
        form.validate({
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
            submitHandler: EditFromAjax,
            invalidHandler: function (event, validator) {
                errorHandler.html(errorHandler.data('orghtml')).show();
            }
        });
    };
    var EditFromAjax = function (form) {
		var $btn = $('[type=submit]', form);
		$btn.button('<i class="fa-li fa fa-spinner fa-spin"></i>');
		$.ajax({
			url:Main.getAjaxFormURL($(form).attr('action')),
			type:$(form).attr('method'),
			data:$(form).serialize(),
			dataType:'json',
			success:function(data){
				$btn.button('reset');
				if(data.hasOwnProperty('status')){
					if(data.status){
						if(data.hasOwnProperty('redirect')){
							window.location.href = data.redirect;
						}
					}else{
						if(data.hasOwnProperty('error')){
							for(var i =0;i!=data.error.length;i++){
								var error = data.error[i];
								var $input = $('[name='+error.input+']');
								var $params = {
									title: 'خطا'
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
    };
    return {
        //main function to initiate template pages
        init: function () {
			Main.SetDefaultValidation();
			runRegisterValidator();
        }
    };
}();
$(function(){
	Register.init();
});
