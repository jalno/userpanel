import "@jalno/translator";
import "select2";

export interface ICountryCode {
	code: string;
	name: string;
	dialingCode: string;
}

export default class Country {
	public static runCountryDialingCodeSelect2($select: JQuery, data: Select2.DataFormat[] | Select2.GroupedDataFormat[], options?: Select2.Options): JQuery {
		const isRTL = Translator.isRTL();
		const defaultOptions: Select2.Options = {
			language: Translator.getActiveShortLang(),
			dir: isRTL ? 'rtl' : 'ltr',
			theme: 'bootstrap',
			data: data,
			width: '100%',
			dropdownAutoWidth: false,
			templateResult: function(state): JQuery {
				const countryCode = (state.id as string || '').toLowerCase();
				const text = state.text.split('-');
				const dialingCode = text[0] || '';
				const countryName = text[1] || '';
				return $(
					`<div style="direction: ltr; text-align: left;">
						<span style="">${dialingCode ? '+' + dialingCode : '<i class="fa fa-globe" aria-hidden="true"></i>'}</span>
						<strong style="${isRTL && "float: right; "}font-family: Vazirmatn; font-size: 1.2em;">
							<span style="margin-right: 10px;">${countryName}</span>
							<span class="flag-icon flag-icon-${countryCode}" style="float: right;"></span>
						</strong>
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
		$select.select2(options).on("change keydown change.select2 select2:open select2:opening select2:selecting", (e) => {
			const inputGroupWidth = $select.parents(".input-group").outerWidth();
			$(".select2-dropdown.select2-dropdown--below, .select2-dropdown.select2-dropdown--above").css("min-width", `${inputGroupWidth}px`)
		});
		return $select;
	}
}