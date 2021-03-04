import "@jalno/translator";
import * as $ from "jquery";
import Helper from "../../Helper";
import IFormAjaxError from "../../IFormAjaxError";

export default class Settings {

	private static readonly $body: JQuery = $("body.users.users-settings, body.profile.profile-settings");
	private static readonly $form: JQuery = $("form#settings_form", Settings.$body);

	public static initIfNeeded(): void {
		if (Settings.$body.length) {
			Settings.init();
		}
	}
	public static init(): void {
		console.log("$form", this.$form);
		Settings.runFormSubmitListener();
	}
	protected static runFormSubmitListener(): void {
		Settings.$form.on("submit", (e) => {
			e.preventDefault();
			Settings.$form.formAjax({
				cache: false,
				processData: false,
				success: (result) => {
					Settings.$form.data("last-form-ajax-success", result);
					Settings.$form.trigger("form-ajax-success", result);
					$.growl.notice({
						title: t("userpanel.success"),
						message: t("userpanel.formajax.success"),
						location: Translator.isRTL() ? "bl" : "br",
					});
				},
				error: (error: IFormAjaxError) => {
					Settings.$form.data("last-form-ajax-error", error);
					Settings.$form.trigger("form-ajax-error", error);
					Helper.defaultFormAjaxErrorHandler(error);
				},
			});
		});
	}
}
