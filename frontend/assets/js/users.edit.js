var UserEdit = function () {
	var runSetDefaultValidation = function () {
        $.validator.setDefaults({
            errorElement: "span",
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.attr("type") == "radio" || element.attr("type") == "checkbox") {
                    error.insertAfter($(element).closest('.form-group').children('div').children().last());
                } else if (element.attr("name") == "card_expiry_mm" || element.attr("name") == "card_expiry_yyyy") {
                    error.appendTo($(element).closest('.form-group').children('div'));
                } else {
                    error.insertAfter(element);
                }
            },
            ignore: ':hidden',
            highlight: function (element) {
                $(element).closest('.help-block').removeClass('valid');
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            success: function (label, element) {
                label.addClass('help-block valid');
                $(element).closest('.form-group').removeClass('has-error');
            },
            highlight: function (element) {
                $(element).closest('.help-block').removeClass('valid');
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            }
        });
	};
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
    };
    return {
        //main function to initiate template pages
        init: function () {
			runSetDefaultValidation();
			runLoginValidator();
        }
    };
}();
$(function(){
	UserEdit.init();
});
