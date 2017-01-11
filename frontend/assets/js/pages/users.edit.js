var UserEdit = function () {

	var runLoginValidator = function () {
        var form = $('#edit_form');
        form.validate({
            rules: {
                name: {
                    required: true
                },
				email: {
                    required: true,
					email:true
                },
                password2: {
					equalTo: 'input[name=password]'
                },
				phone:{
      				digits: true
				},
				cellphone:{
      				digits: true,
					rangelength:[10,12]
				},
				credit:{
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
						$.growl.notice({title:"ثبت شد", message:"اطلاعات این کاربر با موفقیت ویرایش شد."});
					}else{
						if(data.hasOwnProperty('error')){
							for(var i =0;i!=data.error.length;i++){
								var error = data.error[i];
								var $input = $('[name=\"'+error.input+'\"]');
								var $params = {
									title: 'خطا'
								};
								if(error.error == 'data_validation'){
									$params.message = 'داده وارد شده معتبر نیست';
								}
								if($input.length){
									$input.inputMsg($params);
								}else{
									$.growl.error($params);
								}
							}

						}else{
							$.growl.error({title: 'خطا', message: " درخواست شما توسط سرور قبول نشد."});
						}
					}
				}else{
					$.growl.error({title: 'خطا', message: " در حال حاضر سرور پاسخ درخواست شما را به درستی ارسال نمیکند."});
				}
			},
			error:function(){
				$btn.html( $btn.data('orghtml'));
				$btn.prop('disabled', false);
				$.growl.error({title: 'خطا', message: "اتصال به سرور ممکن نمیباشد"});
			}

		});
    };
    return {
        //main function to initiate template pages
        init: function () {
			Main.SetDefaultValidation();
			runLoginValidator();
        }
    };
}();
$(function(){
	UserEdit.init();
});
