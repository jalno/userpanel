import "@jalno/translator";
import * as $ from "jquery";
import "select2";

export default class Search {
	public static initIfNeeded() {
		if (Search.$modal.length) {
			Search.init();
		}
	}
	public static init() {
		Search.initSelect2();
		Search.runSubmitFormListener();
	}

	private static $modal: JQuery = $("#users-search");

	private static initSelect2() {
		$("select[name=type-select]", Search.$modal).select2({
			multiple: true,
			allowClear: true,
			theme: "bootstrap",
			dropdownParent: Search.$modal,
			placeholder: t("userpanel.choose"),
			dir: Translator.isRTL() ? "rtl" : "ltr",
			language: Translator.getActiveShortLang(),
		});
	}
	private static runSubmitFormListener() {
		$("form", Search.$modal).on("submit", function() {
			const types = $("select[name=type-select]", this).val() as string[];
			$("input[name=type]", this).val(types.join(","));
		});
	}
}
