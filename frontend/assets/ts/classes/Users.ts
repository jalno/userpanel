/// <reference path="../definitions/jquery.growl.d.ts" />
import * as $ from "jquery";
import "jquery.growl";
import "bootstrap-inputmsg";
import { webuilder} from "webuilder";
import {Main} from "./Main";
import  "./jquery.formAjax";
class UserForm{
	protected form:JQuery;
	constructor(form:JQuery){
		this.form = form;
	}
	public FromAjax(form){
		$(form).formAjax({
			success: this.successForm,
			error: function(error:webuilder.AjaxError) {
				let $params = {
					title: 'خطا',
					message:''
				}
				if(error.input){
					let $input = $(`[name="${error.input}"]`, $(form));
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
					$.growl.error($params);
				}
			}
		});
	}
	protected runPrivacyVisibilty():void{
		$('.changevisibity', this.form).on('click', function(e){
			e.preventDefault();
			let $button = $(this).parents('.input-group-btn').find('button');
			let field:string = $(this).data('field');
			let visibility:string = $(this).data('visibility');
			$button.html($(this).html()+' <span class="caret"></span>');
			$(`input[name=visibility_${field}]`).val(visibility == 'public' ? '1' : '');
		});
	}
	public successForm(){
		$.growl.notice({title:"ثبت شد", message:"کاربر با موفقیت ذخیره شد."});
	}
	protected init():void{
		Main.SetDefaultValidation();
		this.runPrivacyVisibilty();
	}
}
class UserAdd extends UserForm{
	private runValidator():void{
		this.form.validate({
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
            submitHandler: (form) => {
				this.FromAjax(form);
			}
        });
	}
	public init(){
		super.init();
		this.runValidator();
	}
}
class UserEdit extends UserForm{
	private runValidator():void{
		this.form.validate({
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
            submitHandler: (form) => {
				this.FromAjax(form);
			}
        });
	}
	public init(){
		super.init();
		this.runValidator();
	}
}
class ProfileEdit extends UserForm{
	private runValidator():void{
		this.form.validate({
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
            submitHandler: (form) => {
				this.FromAjax(form);
			}
        });
	}

	public successForm(){
		$.growl.notice({title:"ثبت شد", message:"اطلاعات شما با موفقیت ذخیره شد."});
	}
	public init(){
		super.init();
		this.runValidator();
	}
}
export class Users{
	public static initIfNeeded():void{
		let $body = $('body');
		if($body.hasClass('users_add')){
			let handler = new UserAdd($('#add_form'));
			handler.init();
		}else if($body.hasClass('users_edit')){
			let handler = new UserEdit($('#edit_form'));
			handler.init();
		}else if($body.hasClass('profile_edit')){
			let handler = new ProfileEdit($('#edit_form'));
			handler.init();
		}
	}
}