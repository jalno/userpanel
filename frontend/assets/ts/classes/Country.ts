import "@jalno/translator";
import * as $ from "jquery";
import "select2";

export interface ICountryCode {
	code: string;
	name: string;
	dialingCode: string;
}

export default class Country {
	public static runCountryDialingCodeSelect2($select: JQuery, data: Select2.DataFormat[] | Select2.GroupedDataFormat[], options?: Select2.Options): JQuery {
		const defaultOptions: Select2.Options = {
			dir: Translator.isRTL() ? 'rtl' : 'ltr',
			theme: 'bootstrap',
			data: data,
			width: '100%',
			dropdownAutoWidth: true,
			templateResult: function(state): JQuery {
				const countryCode = (state.id as string || '').toLowerCase();
				const text = state.text.split('-');
				const dialingCode = text[0] || '';
				const countryName = text[1] || '';
				return $(
					`<div style="direction: ltr; text-align: left;">
						<span>${dialingCode ? '+' + dialingCode : '<i class="fa fa-globe" aria-hidden="true"></i>'}</span>
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
						${countryCode ? `<span class="flag-icon flag-icon-${countryCode}"></span>` : ''}
						<span>${dialingCode ? '+' + dialingCode : '<i class="fa fa-globe" aria-hidden="true"></i>'}</span>
					<div>`
				);
			},
		};
		options = {...defaultOptions, ...options};
		$select.select2(options).on("change change.select2 select2:open select2:opening select2:selecting", (e) => {
			const $target = $(e.target);
			const select2width = $target.parents(".input-group-btn").outerWidth();
			const inputWidth = $("input", $target.parents(".input-group")).outerWidth();
			setTimeout(() => {
				$(".select2-dropdown.select2-dropdown--below").css("min-width", `${select2width + inputWidth}px`)
			});
		});
		return $select;
	}
}