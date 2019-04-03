import "bootstrap";
import "bootstrap-inputmsg";
import "jquery.growl";
import * as $ from "jquery";
import AutoComplete from "./../AutoComplete";
import "webuilder";
import { Router } from "webuilder";
import "webuilder/formAjax";

interface IUser {
	id: number;
	name: string;
	lastname?: string;
}

interface IApp {
	id: number;
	name: string;
	token: string;
	user: IUser;
}
enum Status {
	ACTIVE = 1,
	DISABLE = 2,
}
interface IApi {
	id: number;
	user: IUser;
	app: IApp;
	token: string;
	status: Status,
}

export default class Apis {
	public static initIfNeeded() {
		const $body = $("body.userpanel-apis");
		if ($body.length) {
			Apis.$searchForm = $("#apisSearch", $body);
			Apis.$addForm = $("#apis-add", $body);
			Apis.init();
		}
	}
	protected static $searchForm: JQuery;
	protected static $addForm: JQuery;
	protected static init() {
		Apis.runUserAutoComplete();
		Apis.runDeleteApisListener();
		Apis.runEditApisListener();
		if (Apis.$addForm.length) {
			Apis.runAutoGenerateToken();
			Apis.runAddApisSubmitListener();
		}
	}
	protected static runUserAutoComplete() {
		if ($("input[name=search_user_name]", Apis.$searchForm).length) {
			const ac = new AutoComplete($("input[name=search_user_name]", Apis.$searchForm));
			ac.users();
		}
		if ($("input[name=user_name]", Apis.$addForm).length) {
			const ac = new AutoComplete($("input[name=user_name]", Apis.$addForm));
			ac.users();
		}
	}
	protected static runAutoGenerateToken() {
		$(".btn.btn-generate-apis-token", Apis.$addForm).on("click", (e) => {
			e.preventDefault();
			$("input[name=token]", Apis.$addForm).val(Apis.generateToken(32));
		});
	}
	protected static runAddApisSubmitListener() {
		const $btn = $(".btn-submit", Apis.$addForm);
		Apis.$addForm.on("submit", function(e) {
			e.preventDefault();
			$btn.prop("disabled", true);
			$(this).formAjax({
				url: Router.url("userpanel/settings/apis/add?ajax=1"),
				dataType: "json",
				success: (data) => {
					window.location.reload();
				},
				error: (error) => {
					$btn.prop("disabled", false);
					if (error.error === "data_duplicate" || error.error === "data_validation") {
						const $input = $(`[name="${error.input}"]`, this);
						const $params = {
							title: "خطا",
							message: "",
						};
						if (error.error === "data_validation") {
							$params.message = "داده وارد شده معتبر نیست";
						}
						if (error.error === "data_duplicate") {
							$params.message = "داده وارد شده تکراری است";
						}
						if ($input.length) {
							$input.inputMsg($params);
						} else {
							$params.message = "پاسخ سرور نامشخص است";
							$.growl.error($params);
						}
					} else {
						$.growl.error({
							title: "خطا",
							message: "درخواست شما توسط سرور قبول نشد",
						});
					}
				},
			});
		});
	}
	protected static runEditApisListener() {
		const $modal = $("#api-edit");
		const $btn = $(".btn-submit", $modal);
		let api: IApi;
		if ($("input[name=edit_user_name]", $modal).length) {
			const ac = new AutoComplete($("input[name=edit_user_name]", $modal));
			ac.users();
		}
		$('.table-apis tbody [data-action="edit"]').on("click", function(e) {
			e.preventDefault();
			api = $(this).parents("tr").data("api") as IApi;
			$(".api-id", $modal).html(api.id.toString());
			$("input[name=edit_user]", $modal).val(api.user.id);
			$("input[name=edit_token]", $modal).val(api.token);
			$("input[name=edit_user_name]", $modal).val(api.user.name + (api.user.lastname ? " " + api.user.lastname : ""));
			$("select[name=edit_app]", $modal).val(api.app.id);
			$("select[name=edit_status]", $modal).val(api.status);
			$modal.modal("show");
		});
		$("form", $modal).on("submit", function(e) {
			if (!api) {
				$modal.modal("hide");
				return false;
			}
			e.preventDefault();
			$btn.prop("disabled", true);
			$(this).formAjax({
				url: Router.url(`userpanel/settings/apis/${api.id}/edit?ajax=1`),
				dataType: "json",
				type: "POST",
				success: () => {
					window.location.reload();
				},
				error: (error) => {
					$btn.prop("disabled", false);
					if (error.error === "data_duplicate" || error.error === "data_validation") {
						const $input = $(`[name="${error.input}"]`, this);
						const $params = {
							title: "خطا",
							message: "",
						};
						if (error.error === "data_validation") {
							$params.message = "داده وارد شده معتبر نیست";
						}
						if (error.error === "data_duplicate") {
							$params.message = "داده وارد شده تکراری است";
						}
						if ($input.length) {
							$input.inputMsg($params);
						} else {
							$params.message = "پاسخ سرور نامشخص است";
							$.growl.error($params);
						}
					} else {
						$.growl.error({
							title: "خطا",
							message: "درخواست شما توسط سرور قبول نشد",
						});
					}
				},
			});
		});
	}
	protected static runDeleteApisListener() {
		const $modal = $("#api-delete");
		const $btn = $(".btn-submit", $modal);
		let $tr: JQuery;
		$('.table-apis tbody [data-action="delete"]').on("click", function(e) {
			e.preventDefault();
			$tr = $(this).parents("tr");
			const api = $tr.data("api") as IApi;
			$(".api-id", $modal).html(api.id.toString());
			$modal.modal("show");
		});
		$("form", $modal).on("submit", function(e) {
			if (!$tr.length) {
				$modal.modal("hide");
			}
			e.preventDefault();
			$btn.prop("disabled", true);
			const api = $tr.data("api") as IApi;
			$(this).formAjax({
				url: Router.url(`userpanel/settings/apis/${api.id}/delete?ajax=1`),
				dataType: "json",
				type: "POST",
				success: (data) => {
					if ($(".table-apis tbody tr").length > 1) {
						$tr.remove();
						$tr = undefined;
						$modal.modal("hide");
					} else {
						window.location.reload();
					}
				},
				error: (error) => {
					$btn.prop("disabled", false);
					if (error.error === "data_duplicate" || error.error === "data_validation") {
						const $input = $(`[name="${error.input}"]`, this);
						const $params = {
							title: "خطا",
							message: "",
						};
						if (error.error === "data_validation") {
							$params.message = "داده وارد شده معتبر نیست";
						}
						if (error.error === "data_duplicate") {
							$params.message = "داده وارد شده تکراری است";
						}
						if ($input.length) {
							$input.inputMsg($params);
						} else {
							$params.message = "پاسخ سرور نامشخص است";
							$.growl.error($params);
						}
					} else {
						$.growl.error({
							title: "خطا",
							message: "درخواست شما توسط سرور قبول نشد",
						});
					}
				},
			});
		});
	}
	private static generateToken(length:number, number:boolean = true, az:boolean = true , AZ:boolean = true, special:boolean = false){
		const $data = {
			azChar : "abcdefghijklmnopqrstuvwxyz",
			numberChar : "0123456789",
			specialChar : ".-+=_,!@$#*%<>[]{}",
			AZChar:""
		};
		$data.AZChar = $data.azChar.toUpperCase();
		const uses = {
			numberChar: number,
			azChar: az,
			AZChar: AZ,
			specialChar: special
		};
		let token = "";
		let parts = 0;
		if(number) parts++;
		if(az) parts++;
		if(AZ) parts++;
		if(special) parts++;
		for(let i = 0;i < Math.ceil(length/parts); i++){
			for(let chars in uses ){
				const flag = uses[chars];
				if (token.length >= length) {
					break;
				}
				if (flag) {
					token += $data[chars].substr(Math.random() * $data[chars].length -1,1);
				}
			}
		}
		return token;
	}
}
