import * as $ from "jquery";
import {AjaxRequest, Router, webuilder} from "webuilder";

$.fn.formAjax = function(settings: webuilder.AjaxSettings) {
	const $this = $(this);
	const $btn = $("[type=submit]", $this);
	$btn.data("orghtml", $btn.html());
	$btn.html('<i class="fa fa-spinner fa-spin"></i>');
	$btn.prop("disabled", true);

	const newSettings: webuilder.AjaxSettings = {};
	for (const key in settings) {
		if (key !== "success" && key !== "error") {
			newSettings[key] = settings[key];
		}
	}
	if (!settings.hasOwnProperty("url")) {
		newSettings.url = Router.getAjaxFormURL($this.attr("action"));
	}
	if (!settings.hasOwnProperty("type")) {
		newSettings.type = $this.attr("method");
	}
	if (!settings.hasOwnProperty("data")) {
		newSettings.data = $this.serialize();
	}
	if (!settings.hasOwnProperty("dataType")) {
		newSettings.dataType = "json";
	}
	newSettings.success = (data: webuilder.AjaxResponse, textStatus: string, jqXHR: JQueryXHR) => {
		$btn.html($btn.data("orghtml"));
		$btn.prop("disabled", false);
		if (settings.hasOwnProperty("success")) {
			settings.success(data, textStatus, jqXHR);
		}
	};
	newSettings.error = (error: webuilder.AjaxError, jqXHR: JQueryXHR) => {
		$btn.html($btn.data("orghtml"));
		$btn.prop("disabled", false);
		if (settings.hasOwnProperty("error")) {
			settings.error(error, jqXHR);
		}
	};
	return AjaxRequest(newSettings);
};
