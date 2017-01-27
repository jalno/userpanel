var UserEdit = function () {
	var form = $('#add_form');
	var runValidator = function () {
        form.validate({
            rules: {
                name: {
                    required: true
                },
				email: {
                    required: true,
					email:true
                },
				password: {
					required: true
                },
				password2: {
					required: true,
					equalTo: 'input[name=password]'
                },
				phone:{
      				digits: true
				},
				cellphone:{
					required: true,
      				digits: true,
					rangelength:[10,12]
				},
				credit:{
					required: true,
      				digits: true
				}
            },
            submitHandler: EditFromAjax,
            invalidHandler: function (event, validator) {
                //errorHandler.html(errorHandler.data('orghtml')).show();
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

						$.growl.notice({title:"ثبت شد", message:"کاربر با موفقیت اضافه شد"});
						if(data.hasOwnProperty('redirect')){
							setTimeout(function(){
								window.location.href = data.redirect;
							}, 2000);
						}
					}else{
						if(data.hasOwnProperty('error')){
							for(var i =0;i!=data.error.length;i++){
								var error = data.error[i];
								var $input = $('[name=\"'+error.input+'\"]');
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
	var runPrivacyVisibilty = function(){
		$('.changevisibity', form).click(function(e){
			e.preventDefault();
			var $button = $(this).parents('.input-group-btn').find('button');
			var $field = $(this).data('field');
			var $visibility = $(this).data('visibility');
			$button.html($(this).html()+' <span class="caret"></span>');
			$('input[name=visibility_'+$field+']').val($visibility == 'public' ? '1' : '');
		});
	}
    return {
        //main function to initiate template pages
        init: function () {
			Main.SetDefaultValidation();
			runValidator();
			runPrivacyVisibilty();
        }
    };
}();
$(function(){
	UserEdit.init();
});
