import "@jalno/translator";
import * as $ from "jquery";
import "select2";

export interface ICountryCode {
	code: string;
	name: string;
	dialingCode: string;
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
				const countryCode = (state.id as string || '').toLowerCase();
				const text = state.text.split('-');
				const dialingCode = text[0] || '';
				const countryName = text[1] || '';
				return $(
					`<div style="direction: ltr; text-align: left;">
						<span>+${dialingCode}</span>
						<strong><span>${countryName}</span><strong>
						<span class="flag-icon flag-icon-${countryCode}" style="float: right;"></span>
					</div>`
				);
			},
			templateSelection: function(state): JQuery {
				const countryCode = (state.id as string || '').toLowerCase();
				const text = state.text.split('-');
				const dialingCode = text[0] || '';
				return $(
					`<div dir="ltr">
						<span class="flag-icon flag-icon-${countryCode}"></span>
						<span>+${dialingCode}</span>
					<div>`
				);
			},
		}).on("select2:open select2:opening select2:selecting", (e) => {
			const $target = $(e.target);
			const select2width = $target.parents(".input-group-btn").outerWidth();
			const inputWidth = $("input", $target.parents(".input-group")).outerWidth();
			setTimeout(() => {
				$(".select2-dropdown.select2-dropdown--below").css("min-width", `${select2width + inputWidth}px`)
			});
		});
	}
}