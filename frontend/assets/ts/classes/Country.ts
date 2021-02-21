import "@jalno/translator";
import * as $ from "jquery";
import "select2";

export interface ICountry {
	id: number;
	code: string;
	name: string;
	dialing_code: string;
}

export default class Country {

	public static runCountryDialingCodeSelect2($select: JQuery, data: Select2.DataFormat[] | Select2.GroupedDataFormat[]) {
		$select.select2({
			dir: Translator.isRTL() ? 'rtl' : 'ltr',
			theme: 'bootstrap',
			data: data,
			width: '100%',
			dropdownAutoWidth: true,
			minimumResultsForSearch: -1,
			templateResult: function(state): JQuery {
				const text = state.text.split('-');
				return $(
					`<div style="direction: ltr; text-align: left;">
						<span>+${state.id}</span>
						<strong><span>${text[0]}</span><strong>
						<span class="flag-icon flag-icon-${(text[1]||"").toLocaleLowerCase()}" style="float: right;"></span>
					</div>`
				);
			},
			templateSelection: function(state): JQuery {
				const text = state.text.split('-');
				return $(
					`<div>
						<span class="flag-icon flag-icon-${(text[1]||"").toLocaleLowerCase()}"></span>
						<span>+${state.id}</span>
					<div>`
				);
			},
		}).on("select2:open select2:opening select2:selecting", (e) => {
			const $target = $(e.target);
			const select2width = $target.parents(".input-group-btn").outerWidth();
			const inputWidth = $("input", $target.parents(".input-group")).outerWidth();
			const width = select2width + inputWidth;
			setTimeout(() => {
				$(".select2-dropdown.select2-dropdown--below").css({
					width: width + "px !important",
					minWidth: width + "px",
				});
			});
		});
	}
}