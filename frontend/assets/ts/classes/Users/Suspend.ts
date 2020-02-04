import "bootstrap";
import * as $ from "jquery";
import "webuilder/formAjax";
import Activate from "./Activate";
import { Users, Status } from "../Users";

export default class Suspend {
	public static initIfNeeded() {
		Suspend.$btn = $(".btn-suspend-user");
		if (Suspend.$btn.length) {
			Suspend.init();
		}
	}
	public static createButtonAndInit($container: JQuery, user: number) {
		Suspend.$btn = $(`<button class="btn btn-warning btn-suspend-user" type="button">
			<div class="btn-icons">
				<i class="fa fa-check-square"></i>
			</div>
		${t('userpanel.user.suspend')}
		</button>`).appendTo($container);
		Suspend.$btn.data("user", user);
		Suspend.init();
	}
	private static $btn: JQuery;
	private static $modal: JQuery;
	private static user: number;
	private static init() {
		Suspend.user = Suspend.$btn.data("user");
		Suspend.initModal();
		Suspend.initSubmitFormListener();
	}
	private static appendModal() {
		Suspend.$modal = $(`<div class="modal fade" id="user-suspend-modal" tabindex="-1" role="dialog">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h4 class="modal-title">${t("userpanel.user.suspend")}</h4>
		</div>
		<div class="modal-body">
			<form id="user-suspend-form" method="POST">${t('userpanel.user.suspend.confirm')}</form>
		</div>
		<div class="modal-footer">
			<button type="submit" form="user-suspend-form" class="btn btn-warning">${t("userpanel.submit")}</button>
			<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">${t("userpanel.cancel")}</button>
		</div>
	</div>`);
	}
	private static initSubmitFormListener() {
		$("form", Suspend.$modal).on("submit", function(e) {
			e.preventDefault();
			Suspend.$btn.prop("disabled", true);
			$(this).formAjax({
				url: `userpanel/users/edit/${Suspend.user}/suspend?ajax=1`,
				success: () => {
					const $container = Suspend.$btn.parent();
					Suspend.$btn.remove();
					$(".user-status-container").attr("class", Users.getStatusClass(Status.SUSPEND)).html(Users.getStatusText(Status.SUSPEND));
					Suspend.$modal.modal("hide");
					Suspend.$modal.one("hidden.bs.modal", () => {
						setTimeout(() => {
							Suspend.$modal.remove();
						}, 1000);
					});
					Activate.createButtonAndInit($container, Suspend.$btn.data("user"));
				},
				error: () => {
					Suspend.$btn.prop("disabled", false);
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
		Suspend.appendModal();
		Suspend.$btn.on("click", (e) => {
			e.preventDefault();
			Suspend.$modal.modal("show");
		});
	}
}
