import * as $ from "jquery";
import "jquery-validation";
import AutoComplete from "./AutoComplete";

export class Logs {
	public static init(): void {
		const $body = $("body");
		if ($body.hasClass("users-logs")) {
			this.$form = $("#userLogsSearch");
			this.runSearchUserAutoComplete();
		}
	}
	public static initIfNeeded(): void {
		if ($("body").hasClass("users-logs")) {
			this.init();
		}
	}
	private static $form: JQuery;
	private static runSearchUserAutoComplete() {
		const autoComplete = new AutoComplete($("input[name=user_name]", this.$form));
		autoComplete.users();
	}
}
