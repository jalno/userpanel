import "@jalno/translator";
import "bootstrap-inputmsg";
import * as $ from "jquery";
import "jquery.growl";
import IFormAjaxError from "./IFormAjaxError";

export default class Helper {

	public static defaultFormAjaxErrorHandler(error: IFormAjaxError, params?: growl.Options): void {
		const defaultParams: growl.Options = {
			title: t("error.fatal.title"),
			message: t("userpanel.formajax.error"),
		};
		params = {...defaultParams, ...params};
		if (error.error === "data_duplicate" || error.error === "data_validation") {
			params.message = t(error.error);
			const $input = $(`[name="${error.input}"]`);
			if ($input.length) {
				$input.inputMsg(params);
				return;
			}
		} else if (error.message) {
			params.message = error.message;
		} else if (error.code) {
			params.message = t(`error.${error.code}`);
		}
		$.growl.error(params);
	}

}

