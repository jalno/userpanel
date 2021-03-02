import "@jalno/translator";
import * as $ from "jquery";
import "jquery.fancytree";
import "jquery.fancytree/dist/modules/jquery.fancytree.glyph"
import Country, { ICountryCode } from "../Country";
import { IUser } from "../Users";
import Permissions, { IFancyTreeItemType, IUserpanelPermission } from "../Permissions";
import UserTypes, { IUsertype } from "../UserTypes";

declare const userPermissions: IUserpanelPermission[]; // permissions that put on page by dynamic data
declare const countriesCode: ICountryCode[];
declare const defaultCountryCode: string;

export default class Edit {

	private static $body: JQuery = $("body.users.users_edit");
	private static $form: JQuery = $("form#edit_form", Edit.$body);
	private static $permissions: JQuery = $(".change-permissions-container .userpanel-permissions-fancytree-container", Edit.$form);
	private static $editUsertypeWarnModal: JQuery;
	private static user: IUser;
	private static hasCustomPermissions: boolean = false;
	private static canEditPermissions: boolean = false;

	public static initIfNeeded(): void {
		if (Edit.$body.length) {
			Edit.init();
		}
	}
	public static init(): void {
		Edit.user = Edit.$form.data("user") as IUser;
		Edit.canEditPermissions = Edit.$form.data("can-edit-permissions") as boolean;
		if (Edit.canEditPermissions) {
			Edit.hasCustomPermissions = Edit.user.has_custom_permissions;
			Edit.runFancyTree(Permissions.buildFancyTreeItems(userPermissions));
			Permissions.setFancyTreeEvents(Edit.$permissions);
			Edit.appendEditUsertypeWarnModal();
			Edit.setFancyTreeEvents();
		}
		Edit.runSelect2();
		Edit.runValidator();
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
	protected static runValidator(): void {
		Edit.$form.validate({
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
				Edit.submitHandler(form);
			},
		})
	}
	protected static submitHandler(form: HTMLFormElement): void {
		const selectedPermissions = Permissions.getSelectedPermissionsFromFancytree(Edit.$permissions);
		const getFormData = (formElement: HTMLFormElement): FormData => {
			const formData = new FormData(formElement);
			if (selectedPermissions.length) {
				for (const index in selectedPermissions) {
					if (index !== undefined) {
						const permissions = selectedPermissions[index];
						formData.append(`permissions[${index}]`, permissions);
					}
				}
			} else {
				formData.append(`permissions`, "");
			}
			return formData;
		}
		$(form).formAjax({
			data: getFormData(form),
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
	protected static runFancyTree(items: IFancyTreeItemType[]): void {
		Edit.$permissions.fancytree({
			source: items,
			debugLevel: 0, // disabled
			rtl: Translator.isRTL(),
			clickFolderMode: 4, // activate_dblclick_expands
			selectMode: 3, // mutlti_hier
			extensions: ["glyph"],
			glyph: {
				preset: "awesome4",
				map: {
					doc: 'fa-key',
					folder: 'fa-folder',
					folderOpen: 'fa-folder-open',
					checkbox: 'fa-square-o',
					expanderOpen: 'fa-minus-square-o',
					expanderClosed: 'fa-plus-square-o',
					checkboxSelected: 'fa-check-square',
				},
			},
		});
	}
	protected static setFancyTreeEvents(): void {
		Edit.$permissions.on("fancytreeselect", (event, data) => {
			const selectedPermissions = Permissions.getSelectedPermissionsFromFancytree(Edit.$permissions);
			const selectedTypeID = $("select[name=type]", Edit.$form).val() as number;
			Edit.getSelectedUsertypePermissions(selectedTypeID, (permissions => {
				Edit.hasCustomPermissions = false;
				const typePermissions = permissions.filter(obj => obj.value);
				if (selectedPermissions.length !== typePermissions.length) {
					Edit.hasCustomPermissions = true;
				} else {
					const plainPermissions = permissions.map(obj => obj.key);
					for (const p of selectedPermissions) {
						if (plainPermissions.indexOf(p) === -1) {
							Edit.hasCustomPermissions = true;
							break;
						}
					}
				}
				Edit.updateCustomPermissionText();
			}));
		});

		const $usertype = $("select[name=type]", Edit.$form);
		$usertype.data("lastSelectedUsertypeID", $usertype.val() as number);
		$usertype.on("change", (e) => {
			const selectedUsertypeID = $usertype.val() as number;
			$usertype.data("selectedUsertypeID", selectedUsertypeID);
			if (Edit.hasCustomPermissions) {
				e.preventDefault();
				$usertype.val($usertype.data("lastSelectedUsertypeID") as number);
				Edit.$editUsertypeWarnModal.modal("show");
			} else {
				$usertype.data("lastSelectedUsertypeID", selectedUsertypeID);
				Edit.getSelectedUsertypePermissions(selectedUsertypeID, (permissions: IUserpanelPermission[]) => {
					Edit.$permissions.fancytree("option", "source", (Permissions.buildFancyTreeItems(permissions)));
				});
			}
		});
	}
	private static updateCustomPermissionText(): void {
		const $container = $(".change-permissions-container", Edit.$form);
		const $customPermissionsWarn = $(".warning-custom-permissions", $container);
		if (Edit.hasCustomPermissions) {
			if (!$customPermissionsWarn.length) {
				const $el = $(`<i class="fa fa-exclamation-circle warning-custom-permissions tooltips"></i>`);
				$el.appendTo($("h3", $container));
				$el.tooltip({
					title: t("userpanel.users.edit.usertype.custom_permissions.warn_text"),
				});
			} else {
				$customPermissionsWarn.show();
			}
		} else {
			if ($customPermissionsWarn.length) {
				$customPermissionsWarn.hide();
			}
		}
	}
	private static getSelectedUsertypePermissions(usertypeID: number, cb?: (permissions: IUserpanelPermission[]) => void, onError?: (reason: any) => void): void {
		const $usertypeSelect = $("select[name=type]", Edit.$form);
		const $selectedOption = $(`option[value="${usertypeID}"]`, $usertypeSelect);
		const typePermissions = $selectedOption.data("permissions") as IUserpanelPermission[];
		if (typePermissions === undefined) {
			UserTypes.getUsertype(usertypeID, (usertype: IUsertype, allPermissions: string[]) => {
				const permissions: IUserpanelPermission[] = [];
				const usertypePlainPermissions: string[] = usertype.permissions.map(obj => obj.name);
				for (const index in allPermissions) {
					if (index !== undefined) {
						const permission = allPermissions[index];
						permissions.push({
							key: permission,
							value: (usertypePlainPermissions.indexOf(permission) !== -1),
						});
					}
				}
				$selectedOption.data("permissions", permissions);
				if (cb) {
					cb(permissions);
				}
			}, onError);
		} else if (cb) {
			cb(typePermissions);
		}
	}
	private static appendEditUsertypeWarnModal(): void {
		Edit.$editUsertypeWarnModal = $(`<div class="modal fade modal-warning" id="change-usertype-warn-modal" tabindex="-1" data-show="true" role="dialog" data-backdrop="static" data-keyboard="false">
			<div class="modal-header">
				<h4 class="modal-title">${t("userpanel.users.edit.usertype.custom_permissions.warn_modal.title")}</h4>
			</div>
			<div class="modal-body">
				<p>${t("userpanel.users.edit.usertype.custom_permissions.warn_modal.help_text", {
					user_fullname: Edit.user.name + (Edit.user.lastname ? " " + Edit.user.lastname : ""),
				})}</p>
			</div>
			<div class="modal-footer">
				<button form="change-usertype-warn-form" class="btn btn-warning btn-confirm">${t("userpanel.users.edit.usertype.custom_permissions.warn_modal.confirm")}</button>
				<button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">${t("userpanel.cancel")}</button>
			</div>
		</div>`).appendTo("body");

		const $usertypeSelect = $("select[name=type]", Edit.$form);

		Edit.$editUsertypeWarnModal.on("shown", () => {
			/* get permissions from server-side before user's making decision for better UX */
			const selectedUsertypeID = $usertypeSelect.data("selectedUsertypeID") as number;
			Edit.getSelectedUsertypePermissions(selectedUsertypeID);
		});
		
		$(".btn-confirm", Edit.$editUsertypeWarnModal).on("click", function(e) {
			e.preventDefault();
			const $btn = $(this);
			$btn.data("orgHTML", $btn.html());
			$btn.html(`<i class="fa fa-spinner fa-spin"></i> ${t("userpanel.users.edit.usertype.custom_permissions.warn_modal.please_wait")}`);
			$("button", Edit.$editUsertypeWarnModal).prop("disabled", true);
			
			Edit.hasCustomPermissions = false;
			Edit.updateCustomPermissionText();

			const selectedUsertypeID = $usertypeSelect.data("selectedUsertypeID") as number;
			$usertypeSelect.val(selectedUsertypeID).data("lastSelectedOption", selectedUsertypeID);

			Edit.getSelectedUsertypePermissions(selectedUsertypeID, (permissions: IUserpanelPermission[]) => {
				Edit.$permissions.fancytree("option", "source", (Permissions.buildFancyTreeItems(permissions)));
				Edit.$editUsertypeWarnModal.modal("hide");
				$btn.html($btn.data("orgHTML"));
				$("button", Edit.$editUsertypeWarnModal).prop("disabled", false);
			}, () => {
				$btn.html($btn.data("orgHTML"));
				$("button", Edit.$editUsertypeWarnModal).prop("disabled", false);
			});
		});
	}
}
