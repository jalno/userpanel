import "@jalno/translator";
import * as $ from "jquery";
import "jquery.fancytree";
import "jquery.fancytree/dist/modules/jquery.fancytree.glyph"
import Country, { ICountryCode } from "../../Country";
import { IUser } from "../../Users";

declare const countriesCode: ICountryCode[];
declare const defaultCountryCode: string;

export default class Edit {

	private static $body: JQuery = $("body.profile.profile_edit");
	private static $form: JQuery = $("form#edit_form", Edit.$body);
	private static user: IUser;

	public static initIfNeeded(): void {
		if (Edit.$body.length) {
			Edit.init();
		}
	}
	public static init(): void {
		Edit.user = Edit.$form.data("user") as IUser;
		Edit.runSelect2();
	}
	protected static runSelect2() {
		for (const field of ["phone", "cellphone"]) {
			const item = Edit.user[field] as string;
			let selectedCountryCode = defaultCountryCode;
			if (item.indexOf('.') > -1) {
				const splited = item.split('.');
				if (splited[0]) {
					selectedCountryCode = splited[0];
				}
			}
			const data = countriesCode.map((country) => {
				return {
					id: country.code,
					text: country.dialingCode + '-' + country.name,
					selected: country.code === selectedCountryCode,
				};
			});
			Country.runCountryDialingCodeSelect2($(`select[name="${field}[code]"]`), data);
		}
	}
}
