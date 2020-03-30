import "bootstrap";
import * as $ from "jquery";
import "webuilder/formAjax";
import Suspend from "./Suspend";
import { Users, Status } from "../Users";

export default class Activate {
	public static initIfNeeded() {
		Activate.$btn = $(".btn-active-user");
		if (Activate.$btn.length) {
			Activate.init();
		}
	}
	public static createButtonAndInit($container: JQuery, user: number) {
		Activate.$btn = $(`<button class="btn btn-success btn-active-user" type="button">
			<div class="btn-icons">
				<i class="fa fa-check-square"></i>
			</div>
		${t("userpanel.user.activate")}
		</button>`).appendTo($container);
		Activate.$btn.data("user", user);
		Activate.init();
	}
	private static $btn: JQuery;
	private static $modal: JQuery;
	private static user: number;
	private static init() {
		Activate.user = Activate.$btn.data("user");
		Activate.initModal();
	}
	private static appendModal() {
		Activate.$modal = $(`<div class="modal fade" id="user-activate-modal" tabindex="-1" role="dialog">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">${t("userpanel.user.activate")}</h4>
		</div>
		<div class="modal-body">
			<form id="user-activate-form" method="POST">${t('userpanel.user.activate.confirm')}</form>
		</div>
		<div class="modal-footer">
			<button type="submit" form="user-activate-form" class="btn btn-success">${t("userpanel.submit")}</button>
			<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">${t("userpanel.cancel")}</button>
		</div>
	</div>`);
	}
	private static initSubmitFormListener() {
		$("form", Activate.$modal).on("submit", function(e) {
			e.preventDefault();
			Activate.$btn.prop("disabled", true);
			$(this).formAjax({
				url: `userpanel/users/edit/${Activate.user}/activate?ajax=1`,
				success: () => {
					const $container = Activate.$btn.parent();
					Activate.$btn.remove();
					$(".user-status-container").attr("class", Users.getStatusClass(Status.ACTIVE)).html(Users.getStatusText(Status.ACTIVE));
					Activate.$modal.modal("hide");
					Activate.$modal.one("hidden.bs.modal", () => {
						setTimeout(() => {
							Activate.$modal.remove();
						}, 1000);
					});
					Suspend.createButtonAndInit($container, Activate.$btn.data("user"));
				},
				error: () => {
					Activate.$btn.prop("disabled", false);
					$.growl.error({
						title: t("error.fatal.title"),
						message: t("userpanel.formajax.error"),
						location: "bl",
					});
				},
			});
		});
	}
	private static initModal() {
		Activate.appendModal();
		Activate.$btn.on("click", (e) => {
			e.preventDefault();
			Activate.$modal.modal("show");
		});
		Activate.initSubmitFormListener();
	}
}
