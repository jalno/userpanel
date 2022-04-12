import "@jalno/translator";
import * as $ from "jquery";
import "select2";
import "jalali-daterangepicker";
import * as moment from "jalali-moment";
import Country, { ICountryCode } from "../Country";

declare const countriesCode: ICountryCode[];
declare const defaultCountryCode: string;

export default class Search {
	public static initIfNeeded() {
		if (Search.$modal.length) {
			Search.init();
		}
	}
	public static init() {
		Search.initSelect2();
		Search.runSubmitFormListener();
		Search.runRegisteredAtDateRangePicker();
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
		const countries = countriesCode.map((country) => {
			return {
				id: country.code,
				text: country.dialingCode + '-' + country.name,
				selected: false,
			};
		});
		countries.unshift({
			id: '',
			text: '',
			selected: true,
		});
		Country.runCountryDialingCodeSelect2(
			$(`select[name="cellphone[code]"]`),
			countries,
			{
				dropdownParent: Search.$modal,
			}
		);
	}
	private static runSubmitFormListener() {
		$("form", Search.$modal).on("submit", function() {
			const types = $("select[name=type-select]", this).val() as string[];
			$("input[name=type]", this).val(types.join(","));
		});
	}

	private static runRegisteredAtDateRangePicker() {
		const lang = Translator.getActiveShortLang();
		if (lang !== 'en') {
			moment.locale(lang);
		}

		const config: any = {
			autoUpdateInput: false,
			showDropdowns: false,
			singleDatePicker: false,
			moment: moment,
			maxDate: moment(),
			opens: 'left',
			locale: {
				applyLabel: t('userpanel.action'),
				cancelLabel: t('userpanel.cancel'),
			},
			parentEl: "#users-search .modal-body",
		};
		
		if (lang === 'fa') {
			config.locale = {
				format: "YYYY/MM/DD",
				monthNames: (moment.localeData() as any)._jMonthsShort,
				firstDay: 6,
				direction: "rtl",
				separator: " - ",
				applyLabel: t('userpanel.action'),
				cancelLabel: t('userpanel.cancel'),
			}
		}

		$('input[name="register"]', Search.$modal).daterangepicker(config, function(start, end) {
			$(this.element).val(start.format("YYYY/MM/DD 00:00:00") + "-" + end.format("YYYY/MM/DD 23:59:59"));
		});
	}
}
