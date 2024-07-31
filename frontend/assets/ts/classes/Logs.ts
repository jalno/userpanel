import "jquery-validation";
import AutoComplete from "./AutoComplete";

export class Logs {
	public static initIfNeeded(): void {
		if ($("body").hasClass("users-logs")) {
			this.init();
		}
	}
	public static init(): void {
		const $body = $("body");
		if ($body.hasClass("users-logs")) {
			this.$form = $("#userLogsSearch");
			this.runSearchUserAutoComplete();
			this.runSystemCheckboxListener();
		}
	}
	private static $form: JQuery;
	private static runSystemCheckboxListener(): void {
		const $user = $("input[name=user_name], input[name=user]", this.$form);
		$("input[name=system_logs]", this.$form).on("change", function() {
			const isChecked = $(this).prop("checked") as boolean;
			$user.prop("disabled", isChecked);
			if (isChecked) {
				$user.val("");
			} else {
				$user.focus();
			}
		}).trigger("change");
	}
	private static runSearchUserAutoComplete() {
		const autoComplete = new AutoComplete($("input[name=user_name]", this.$form));
		autoComplete.users();
	}
}
