import "@jalno/translator";
import "bootstrap-inputmsg";
import * as $ from "jquery";
import "webuilder";

export default class Settings {
	public static initIfNeeded() {
		Settings.$form = $(".userpanel-settings #general-settings-form");
		if (Settings.$form.length) {
			Settings.init();
		}
	}

	private static $form: JQuery;

	private static init() {
		Settings.runSubmitFormListener();
	}
	private static runSubmitFormListener() {
		Settings.$form.on("submit", function(e) {
			e.preventDefault();
			$(this).formAjax({
				success: () => {
					$.growl.notice({
						title: t("userpanel.success"),
						message: t("userpanel.formajax.success"),
					});
				},
				error: (response) => {
					if (response.error === "data_duplicate" || response.error === "data_validation") {
						const $input = $(`[name="${response.input}"]`, this);
						const params = {
							title: t("error.fatal.title"),
							message: "",
						};
						if (response.error === "data_validation") {
							params.message = t("data_validation");
						}
						if (response.error === "data_duplicate") {
							params.message = t("data_duplicate");
						}
						if ($input.length) {
							$input.inputMsg(params);
						} else {
							$.growl.error(params);
						}
					} else {
						$.growl.error({
							title: t("error.fatal.title"),
							message: t("packages.nopo.error.server_error"),
						});
					}
				},
			});
		});
	}
}
