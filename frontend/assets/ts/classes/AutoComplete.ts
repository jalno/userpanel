import "jquery-ui/dist/jquery-ui.js";
import { AjaxRequest } from "webuilder";

export default class AutoComplete {
	private $element: JQuery;
	private $input: JQuery;

	public constructor($element: JQuery|string) {
		if (typeof $element === "string") {
			$element = $($element);
		}
		let input = $element.attr("name");
		input = input.substring(0, input.lastIndexOf("_"));
		this.$element = $element;
		this.$input = $element.parents("form").find(`input[name="${input}"]`);
	}
	public users() {
		this.runAutocomplete("userpanel/users", (ul: any, item: any) => {
			return $("<li>")
				.append("<strong>" + item.name + (item.lastname ? " " + item.lastname : "") + `</strong><small class="ltr">` + item.email + `</small><small class="ltr">` + item.cellphone + "</small>")
				.appendTo(ul);
		}, (_event, ui) => {
			this.$element.val(ui.item.name + (ui.item.lastname ? " " + ui.item.lastname : ""));
			this.$input.val(ui.item.id).trigger("change");
			return false;
		});
	}
	private runAutocomplete(url: string, render: (ul: any, item: any) => void, select: JQueryUI.AutocompleteEvent) {
		this.$element.autocomplete({
			source: (request: any, response: any) => {
				AjaxRequest({
					url: url,
					data: {
						word: request.term,
					},
					success: (data) => {
						response(data.items);
					},
				});
			},
			select: select,
			focus: select,
			create: function() {
				$(this).data("ui-autocomplete")._renderItem  = render;
			},
		});
	}
}
