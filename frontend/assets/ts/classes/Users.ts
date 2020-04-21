// tslint:disable-next-line: no-reference
/// <reference path="../definitions/jquery.growl.d.ts" />

import "@jalno/translator";
import {AvatarPreview} from "bootstrap-avatar-preview/AvatarPreview";
import "bootstrap-inputmsg";
import * as moment from "jalali-moment";
import * as $ from "jquery";
import "jquery.growl";
import { AjaxRequest, Router, webuilder} from "webuilder";
import "./jquery.formAjax";
import {Main} from "./Main";
import Activate from "./Users/Activate";
import Suspend from "./Users/Suspend";

export enum Status {
	DEACTIVE,
	ACTIVE,
	SUSPEND,
}

class UserForm {
	protected form: JQuery;
	constructor(form: JQuery) {
		this.form = form;
	}
	public FromAjax(form) {
		$(form).formAjax({
			data: new FormData(form),
			contentType: false,
			processData: false,
			success: this.successForm,
			error: (error: webuilder.AjaxError) => {
				const params = {
					title: t("error.fatal.title"),
					message: "",
				};
				if (error.input) {
					const $input = $(`[name="${error.input}"]`, $(form));
					if (error.error === "data_duplicate") {
						params.message = t(`user.${error.input}.data_duplicate`);
					} else if (error.error === "data_validation") {
						params.message = t("data_validation");
					}
					if ($input.length) {
						$input.inputMsg(params);
					} else {
						$.growl.error(params);
					}
				} else {
					$.growl.error(params);
				}
			},
		});
	}
	public successForm() {
		$.growl.notice({title: t("userpanel.success"), message: t("userpanel.users.save")});
	}
	protected runPrivacyVisibilty(): void {
		$(".changevisibity", this.form).on("click", function(e) {
			e.preventDefault();
			const $button = $(this).parents(".input-group-btn").find("button");
			const field: string = $(this).data("field");
			const visibility: string = $(this).data("visibility") as string;
			$button.html($(this).html() + ' <span class="caret"></span>');
			$(`input[name=visibility_${field}]`).val(visibility === "public" ? "1" : "");
		});
	}
	protected init(): void {
		Main.SetDefaultValidation();
		this.runPrivacyVisibilty();
	}
}
// tslint:disable-next-line: max-classes-per-file
class UserAdd extends UserForm {
	public init() {
		super.init();
		this.runValidator();
	}
	private runValidator(): void {
		this.form.validate({
			rules: {
				name: {
					required: true,
				},
				email: {
					required: true,
					email: true,
				},
				password: {
					required: true,
				},
				password2: {
					required: true,
					equalTo: "input[name=password]",
				},
				phone: {
					digits: true,
				},
				cellphone: {
					required: true,
					rangelength: [10, 13],
				},
				credit: {
					required: true,
						digits: true,
				},
			},
			submitHandler: (form) => {
				this.FromAjax(form);
			},
		});
	}
}
// tslint:disable-next-line: max-classes-per-file
class UserEdit extends UserForm {
	public init() {
		super.init();
		this.runValidator();
	}
	private runValidator(): void {
		this.form.validate({
			rules: {
				name: {
					required: true,
				},
				email: {
					required: true,
					email: true,
				},
				password2: {
					equalTo: "input[name=password]",
				},
				phone: {
					digits: true,
				},
				cellphone: {
					rangelength: [10, 13],
				},
				credit: {
					number: true,
				},
			},
			submitHandler: (form) => {
				this.FromAjax(form);
			},
		});
	}
}
// tslint:disable-next-line: max-classes-per-file
class ProfileEdit extends UserForm {
	public init() {
		super.init();
		this.runValidator();
	}
	public successForm() {
		$.growl.notice({title: t("userpanel.success"), message: t("userpanel.users.update") });
	}
	private runValidator(): void {
		this.form.validate({
			rules: {
				name: {
					required: true,
				},
				lastname: {
					required: true,
				},
				password2: {
					equalTo: "input[name=password]",
				},
				phone: {
					digits: true,
					required: true,
				},
				city: {
					required: true,
				},
				zip: {
					digits: true,
					required: true,
				},
				address: {
					required: true,
				},
			},
			submitHandler: (form) => {
				this.FromAjax(form);
			},
		});
	}
}
// tslint:disable-next-line: max-classes-per-file
class RunAvatarPreview {
	protected form: JQuery;
	constructor(form: JQuery) {
		this.form = form;
	}
	public init() {
		this.runAvatarPreview();
	}
	private runAvatarPreview(): void {
		const avatarPreview = new AvatarPreview($(".user-image", this.form));
	}
}
// tslint:disable-next-line: max-classes-per-file
class UserView {
	protected runAvatarPreview: RunAvatarPreview;
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
		this.runAvatarPreview = new RunAvatarPreview(this.form);
	}
	public init() {
		this.avatarListener();
		this.formSubmitListener();
		this.runAvatarPreview.init();
		this.initActivityCalendar();
		this.setUserActivityEvents();
	}
	private formSubmitListener() {
		this.form.on("submit", function(e) {
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
				error: (error: webuilder.AjaxError) => {
					if (error.error === "data_duplicate" || error.error === "data_validation") {
						const $input = $(`[name="${error.input}"]`);
						const params = {
							title: t("error.fatal.title"),
							message: t(error.error),
						};
						if ($input.length) {
							$input.inputMsg(params);
						} else {
							$.growl.error(params);
						}
					} else {
						$.growl.error({
							title: t("error.fatal.title"),
							message: t("userpanel.formajax.error"),
						});
					}
				},
			});
		});
	}
	private avatarListener(): void {
		$(".user-image", this.form).on("bootstrap.avatar.preview.change", () => {
			this.form.submit();
		});
		$(".user-image", this.form).on("bootstrap.avatar.preview.remove", () => {
			if ($("input[name=avatar_remove]", this.form).length) {
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
		this.userActivityData.activity = (this.userActivityData.timeFrom && this.userActivityData.timeUntil) ? "true" : "false";
		AjaxRequest({
			url: "userpanel/logs",
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
							<div class="time tooltips" data-title=" ${moment(item.time * 1000).locale("fa").fromNow()}">
								<i class="fa fa-time bigger-110"></i>  ${moment(item.time * 1000).locale("fa").format("HH:mm:ss YYYY/MM/DD")}
							</div>
						</a>
					</li>`);
				}
				$panel.mCustomScrollbar("update");
				$(".tooltips", $panel).tooltip();
			},
			error: () => {
				if ($spinner.length) {
					$spinner.remove();
				}
				$.growl.error({
					title: t("error.fatal.title"),
					message: t("userpanel.formajax.error"),
				});
			},
		});
	}
	private setUserActivityEvents() {
		const $panel = $(".panel-activity .panel-scroll");
		$panel.mCustomScrollbar("destroy");
		const panelHeight = $panel.height();
		const that = this;
		$panel.mCustomScrollbar({
			axis: "y",
			theme: "minimal-dark",
			mouseWheel: {
				enable: true,
			},
			callbacks: {
				whileScrolling: () => {
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
				},
			},
		});
		$panel.scroll(() => {

		});
	}
}
// tslint:disable-next-line: max-classes-per-file
export class Users {
	public static initIfNeeded(): void {
		Activate.initIfNeeded();
		Suspend.initIfNeeded();
		const $body = $("body");
		if ($body.hasClass("users_add")) {
			const handler = new UserAdd($("#add_form"));
			handler.init();
		} else if ($body.hasClass("users_edit")) {
			const handler = new UserEdit($("#edit_form"));
			handler.init();
		} else if ($body.hasClass("profile_edit")) {
			const handler = new ProfileEdit($("#edit_form"));
			handler.init();
			const avatarHandler = new RunAvatarPreview($("#edit_form"));
			avatarHandler.init();
		} else if ($body.hasClass("users_view")) {
			const handler = new UserView($(".user_image"));
			handler.init();
		} else if ($body.hasClass("profile_view")) {
			const handler = new UserView($(".profile_image"));
			handler.init();
		}
	}
	public static getStatusClass(status: Status) {
		switch (status) {
			case Status.DEACTIVE:
				return "label user-status-container label-inverse";
			case Status.ACTIVE:
				return "label user-status-container label-success";
			case Status.SUSPEND:
				return "label user-status-container label-warning";
		}
	}
	public static getStatusText(status: Status) {
		switch (status) {
			case Status.DEACTIVE:
				return t("user.status.deactive");
			case Status.ACTIVE:
				return t("user.status.active");
			case Status.SUSPEND:
				return t("user.status.suspend");
		}
	}
}
