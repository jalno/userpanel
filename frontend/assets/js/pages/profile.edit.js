var ProfileEdit = function () {
	var form = $('#edit_form');
	var runLoginValidator = function () {
        form.validate({
            rules: {
                name: {
                    required: true
                },
                lastname: {
                    required: true
                },
                password2: {
					equalTo: 'input[name=password]'
                },
				phone:{
      				digits: true,
					required: true
				},
				city:{
      				required: true
				},
				zip:{
					digits: true,
      				required: true
				},
				address:{
      				required: true
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
		var data = new FormData(form);
		$.ajax({
			url:Main.getAjaxFormURL($(form).attr('action')),
			type:$(form).attr('method'),
			data: data,
			dataType:'json',
			processData: false,
			contentType: false,
			success:function(data){
				$btn.button('reset');
				if(data.hasOwnProperty('status')){
					if(data.status){
						$.growl.notice({title:"ثبت شد", message:"اطلاعات شما با موفقیت ویرایش شد."});
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
			runLoginValidator();
			runPrivacyVisibilty();
        }
    };
}();
$(function(){
	ProfileEdit.init();
});
