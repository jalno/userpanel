import "@jalno/translator";
import * as $ from "jquery";
import "bootstrap-inputmsg";
import "select2";
import Country, { ICountryCode } from "../Country";

declare const countriesCode: ICountryCode[];
declare const defaultCountryCode: string;

export default class Add {

	private static $body: JQuery = $("body.users.users_add");
	private static $form: JQuery = $("form#add_form", Add.$body);

	public static initIfNeeded(): void {
		if (Add.$body.length) {
			Add.init();
		}
	}
	public static init(): void {
		Add.runValidator();
		Add.runSelect2();
	}
	protected static runSelect2() {
		Country.runCountryDialingCodeSelect2(
			$(`select[name="phone[code]"], select[name="cellphone[code]"]`),
			countriesCode.map((country) => {
				return {
					id: country.code,
					text: country.dialingCode + '-' + country.name,
					selected: country.code === defaultCountryCode,
				};
			}),
		);
	}
	protected static runValidator(): void {
		Add.$form.validate({
			rules: {
				name: {
					required: true,
				},
				email: {
					required: true,
					email: true,
				},
				password2: {
					equalTo: "input[name=password]",
				},
				phone: {
					digits: true,
				},
				cellphone: {
					rangelength: [10, 13],
				},
				credit: {
					number: true,
				},
			},
			submitHandler: (form) => {
				Add.submitHandler(form);
			},
		})
	}
	protected static submitHandler(form: HTMLFormElement): void {
		$(form).formAjax({
			data: new FormData(form),
			contentType: false,
			processData: false,
			success: (data) => {
				$.growl.notice({
					title: t("userpanel.success"),
					message: t("userpanel.formajax.success"),
				});
			},
			error: (error: webuilder.AjaxError) => {
				const params: growl.Options = {
					title: t("error.fatal.title"),
					message: t("userpanel.formajax.error"),
				};
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
			},
		});
	}
}