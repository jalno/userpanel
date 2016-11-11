$(function(){
	$('.fileupload input[type=file]').on('change', function(event) {
		var form = $(this).parents('form');
		var data = new FormData(form[0]);
		$.ajax({
			url:Main.getAjaxFormURL($(form).attr('action')),
			type:$(form).attr('method'),
			data:data,
			dataType:'json',
			processData: false,
			contentType: false,
			success:function(data){
				if(data.hasOwnProperty('status')){
					if(data.status){
						$.growl.notice({title:"ثبت شد", message:"اطلاعات شما با موفقیت ویرایش شد."});
					}else{
						if(data.hasOwnProperty('error')){
							for(var i =0;i!=data.error.length;i++){
								var error = data.error[i];
								var $input = $('[name='+error.input+']');
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
				$.growl.error({title: 'خطا', message: "اتصال به سرور ممکن نمیباشد"});

			}

		});
	});
})
