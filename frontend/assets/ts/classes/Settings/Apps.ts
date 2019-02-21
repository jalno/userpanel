import "bootstrap";
import * as $ from "jquery";
import AutoComplete from "./../AutoComplete";
import "webuilder";
import { Router } from "webuilder";
import "webuilder/formAjax";

export default class Apps {
	public static initIfNeeded() {
		const $body = $("body.userpanel-apps");
		if ($body.length) {
			Apps.$searchForm = $("#appsSearch", $body);
			Apps.$addForm = $("#apps-add", $body);
			Apps.init();
		}
	}
	protected static $searchForm: JQuery;
	protected static $addForm: JQuery;
	protected static init() {
		Apps.runUserAutoComplete();
		Apps.runDeleteAppsListener();
		if (Apps.$addForm.length) {
			Apps.runAutoGenerateToken();
			Apps.runAddAppsSubmitListener();
		}
	}
	protected static runUserAutoComplete() {
		if ($("input[name=search_user_name]", Apps.$searchForm).length) {
			const ac = new AutoComplete($("input[name=search_user_name]", Apps.$searchForm));
			ac.users();
		}
		if ($("input[name=user_name]", Apps.$addForm).length) {
			const ac = new AutoComplete($("input[name=user_name]", Apps.$addForm));
			ac.users();
		}
	}
	protected static runAutoGenerateToken() {
		$(".btn.btn-generate-apps-token", Apps.$addForm).on("click", (e) => {
			e.preventDefault();
			$("input[name=token]", Apps.$addForm).val(Apps.generateToken(32));
		});
	}
	protected static runAddAppsSubmitListener() {
		const $btn = $(".btn-submit", Apps.$addForm);
		Apps.$addForm.on("submit", function(e) {
			e.preventDefault();
			$btn.prop("disabled", true);
			$(this).formAjax({
				url: Router.url("userpanel/settings/apps/add?ajax=1"),
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
	protected static runDeleteAppsListener() {
		const $modal = $("#app-delete");
		const $btn = $("btn-submit", $modal);
		let $tr: JQuery;
		$('.table-apps tbody [data-action="delete"]').on("click", function(e) {
			$tr = $(this).parents("tr");
			$(".app-id", $modal).html($tr.data("app-id"));
			$modal.modal("show");
		});
		$("form", $modal).on("submit", function(e) {
			if (!$tr.length) {
				$modal.modal("close");
			}
			e.preventDefault();
			$btn.prop("disabled", true);
			$(this).formAjax({
				url: Router.url(`userpanel/settings/apps/${$tr.data("app-id")}/delete?ajax=1`),
				dataType: "json",
				type: "POST",
				success: (data) => {
					if ($(".table-apps tbody tr").length > 1) {
						$tr.remove();
						$tr = undefined;
						$modal.modal("close");
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
