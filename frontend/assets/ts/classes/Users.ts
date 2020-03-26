/// <reference path="../definitions/jquery.growl.d.ts" />
import "@jalno/translator";
import * as $ from "jquery";
import * as moment from "jalali-moment";
import "jquery.growl";
import "bootstrap-inputmsg";
import { webuilder, AjaxRequest, Router} from "webuilder";
import {Main} from "./Main";
import  "./jquery.formAjax";
import {AvatarPreview} from 'bootstrap-avatar-preview/AvatarPreview';
import Activate from "./Users/Activate";
import Suspend from "./Users/Suspend";

export enum Status {
	DEACTIVE,
	ACTIVE,
	SUSPEND,
}

class UserForm{
	protected form:JQuery;
	constructor(form:JQuery){
		this.form = form;
	}
	public FromAjax(form){
		$(form).formAjax({
			data: new FormData(form),
			contentType: false,
			processData: false,
			success: this.successForm,
			error: function(error:webuilder.AjaxError) {
				let $params = {
					title: t("error.fatal.title"),
					message:''
				}
				if(error.input){
					let $input = $(`[name="${error.input}"]`, $(form));
					if (error.error == 'data_duplicate') {
						$params.message = t(`user.${error.input}.data_duplicate`);
					} else if (error.error == 'data_validation') {
						$params.message = t("data_validation");
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
		$.growl.notice({title: t("userpanel.success"), message: t("userpanel.users.save")});
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
      				integer: true
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
		$.growl.notice({title: t("userpanel.success"), message: t("userpanel.users.update") });
	}
	public init(){
		super.init();
		this.runValidator();
	}
}
class runAvatarPreview {
	protected form:JQuery;
	constructor(form:JQuery){
		this.form = form;
	}
	private runAvatarPreview():void{
		new AvatarPreview($('.user-image', this.form));
	}
	public init(){
		this.runAvatarPreview();
	}
}
class UserView {
	protected runAvatarPreview: runAvatarPreview;
	private userActivityData = {
		ajax: 1,
		user: undefined,
		timeFrom: undefined,
		timeUntil: undefined,
		activity: "true",
		page: 1,
		ipp: 50,
	};
	private preventLoadUserActivity = false;
	public constructor(protected form: JQuery) {
		this.runAvatarPreview = new runAvatarPreview(this.form);
	}
	private formSubmitListener(){
		this.form.on('submit', function(e){
			e.preventDefault();
			$(this).formAjax({
				data: new FormData(this),
				contentType: false,
				processData: false,
				success: (data: webuilder.AjaxResponse) => {
					$.growl.notice({
						title: t("userpanel.success"),
						message: t("userpanel.formajax.success"),
					});
				},
				error: function(error:webuilder.AjaxError){
					if(error.error == 'data_duplicate' || error.error == 'data_validation'){
						let $input = $('[name='+error.input+']');
						let $params = {
							title: t("error.fatal.title"),
							message: t(error.error),
						};
						if($input.length){
							$input.inputMsg($params);
						}else{
							$.growl.error($params);
						}
					}else{
						$.growl.error({
							title: t("error.fatal.title"),
							message: t("userpanel.formajax.error"),
						});
					}
				}
			});
		});
	}
	private avatarListener():void{
		$('.user-image', this.form).on('bootstrap.avatar.preview.change', () => {
			this.form.submit();
		});
		$('.user-image', this.form).on('bootstrap.avatar.preview.remove', () =>{
			if($('input[name=avatar_remove]', this.form).length){
				this.form.submit();
			}
		});
	}
	private initActivityCalendar() {
		const that = this;
		$(".calender .calendar-square").on("click", function(e) {
			e.preventDefault();
			const $this = $(this);
			if ($this.hasClass("calendar-square-empty") || $this.hasClass("color0")) {
				return;
			}
			that.userActivityData.page = 1;
			that.userActivityData.timeFrom = $(this).data("from");
			that.userActivityData.timeUntil = $(this).data("until");
			that.getUserActivities();
		});
	}
	public init(){
		this.avatarListener();
		this.formSubmitListener();
		this.runAvatarPreview.init();
		this.initActivityCalendar();
		this.setUserActivityEvents();
	}
	private getUserActivities() {
		const spinner = `<li class="text-center mt-30"><i class="fa fa-3x fa-spinner fa-spin"></i></li>`;
		const $panel = $(".panel-activity .panel-scroll .mCSB_container");
		let $ul = $(".activities", $panel);
		if (!$ul.length) {
			$ul = $(`<ul class="activities"></ul>`).appendTo($panel);
		}
		if (this.userActivityData.page === 1) {
			this.preventLoadUserActivity = false;
			$ul.html("");
		}
		const $spinner = $(spinner).appendTo($ul);
		this.userActivityData.user = $(".panel-activity").data("user");
		AjaxRequest({
			url: 'userpanel/logs',
			data: this.userActivityData,
			success: (response) =>  {
				this.preventLoadUserActivity = this.userActivityData.page >= Math.ceil(response.total_items / response.items_per_page);
				if ($spinner.length) {
					$spinner.remove();
				}
				for (const item of response.items) {
					$ul.append(`<li>
						<a class="activity" href="${response.permissions.canView ? Router.url("userpanel/logs/view/" + item.id) : "#"}">
							<i class="circle-icon ${item.icon} ${item.color}"></i> <span class="desc">${item.title}</span>
							<div class="time">
								<i class="fa fa-time bigger-110"></i>  ${moment(item.time * 1000).locale("fa").fromNow()}
							</div>
						</a>
					</li>`);
				}
				$panel.mCustomScrollbar("update");
			},
		});
	}
	private setUserActivityEvents() {
		const $panel = $(".panel-activity .panel-scroll");
		$panel.mCustomScrollbar("destroy");
		const panelHeight = $panel.height();
		const that = this;
		$panel.mCustomScrollbar({
			axis:"y",
			theme:"minimal-dark",
			mouseWheel:{
				enable:true
			},
			callbacks: {
				whileScrolling: function() {
					if (that.preventLoadUserActivity) {
						return;
					}
					const $container = $(".mCSB_container", $panel);
					const height = $container.height() - panelHeight;
					const top = Math.abs(parseInt($container.css("top"), 10));
					if (top * 100 / height > 80) {
						that.userActivityData.page++;
						that.preventLoadUserActivity = true;
						that.getUserActivities();
					}
				}
			}
		});
		$panel.scroll(function() {
			
		});
	}
}
export class Users {
	public static getStatusClass(status: Status) {
		switch (status) {
			case Status.DEACTIVE:
				return 'label user-status-container label-inverse';
			case Status.ACTIVE:
				return 'label user-status-container label-success';
			case Status.SUSPEND:
				return 'label user-status-container label-warning';
		}
	}
	public static getStatusText(status: Status) {
		switch (status) {
			case Status.DEACTIVE:
				return t('user.status.deactive');
			case Status.ACTIVE:
				return t('user.status.active');
			case Status.SUSPEND:
				return t('user.status.suspend');
		}
	}
	public static initIfNeeded():void{
		Activate.initIfNeeded();
		Suspend.initIfNeeded();
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
			let avatarHandler = new runAvatarPreview($('#edit_form'));
			avatarHandler.init();
		}else if($body.hasClass('users_view')){
			let handler = new UserView($('.user_image'));
			handler.init();
		}else if($body.hasClass('profile_view')){
			let handler = new UserView($('.profile_image'));
			handler.init();
		}
	}
}
