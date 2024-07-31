import Add from "./UserTypes/Add";
import Edit from "./UserTypes/Edit";
import { AjaxRequest, Router, webuilder } from "webuilder";
import Permissions, { IFancyTreeItemType } from "./Permissions";

export interface IUsertype {
	id: number;
	name: string;
	permissions: IPermission[];
	children: IPriority[];
}

export interface IPermission {
	type: number;
	name: string;
}

export interface IPriority {
	parent: number;
	child: number;
}

export default class UserTypes {
	public static initIfNeeded(): void {
		UserTypes.init();
	}
	public static init(): void {
		Add.initIfNeeded();
		Edit.initIfNeeded();
	}

	public static getUsertype(usertypeID: number, onSuccess: (usertype: IUsertype, allPermissions: string[]) => void, onError?: (reason: webuilder.AjaxError) => void): JQueryXHR {
		return AjaxRequest({
			url: Router.url(`userpanel/settings/usertypes/view/${usertypeID}?ajax=1`),
			success: (data: {
				status: true,
				usertype: IUsertype,
				all_permissions: string[],
			}) => {
				onSuccess(data.usertype, data.all_permissions);
			},
			error: (reason) => {
				if (onError) {
					onError(reason);
				}
			},
		})
	}

	public static runFancyTree($permissions: JQuery, items: IFancyTreeItemType[]) {
		$permissions.fancytree({
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
				}
			},
		});
	}
	
	public static runsubmitFormListener($form: JQuery, $permissions: JQuery) {
		$form.on("submit", () => {

			$('input[name="permissions[]"]', $permissions).remove();

			const selectedPermissions = Permissions.getSelectedPermissionsFromFancytree($permissions);
			let html = "";
			for (const permission of selectedPermissions) {
				html += `<input type="hidden" name=permissions[] value="${permission}">`;
			}
			$(html).appendTo($permissions);
		});
	}

	public static runCopyFromAnothoerUsertypeListener($select: JQuery, $permissions: JQuery) {

		const el = `<span class="help-block"><div class="help-block-icon"><i class="fa fa-spinner fa-spin"></i></div> ${t("loading")}</span>`

		let $el: JQuery;

		const $form = $select.parents("form");

		$select.on("change", function() {

			const usertype = parseInt($(this).val(), 10);

			if (!isNaN(usertype)) {

				$(this).prop("disabled", true);
				$permissions.fancytree("option", "disabled", true);
				$el = $(el).insertAfter(this);

				UserTypes.getUsertype(usertype, (usertype, permissions) => {

					$(this).prop("disabled", false);
					$permissions.fancytree("option", "disabled", false);
					$el.remove();

					const selectedPermissions = usertype.permissions.map((permission) => permission.name);

					$permissions.fancytree("option", "source", Permissions.buildFancyTreeItems(permissions.map((permission) => {
						return {
							key: permission,
							value: selectedPermissions.indexOf(permission) > -1,
						};
					})));

					const children = usertype.children.map(item => item.child);

					$(`input[name="priorities[]"]`, $form).each(function() {
						$(this).prop("checked", children.indexOf(parseInt($(this).val())) > -1).trigger("change");
					});

					$(this).val("");
				}, () => {

					$(this).prop("disabled", false);
					$permissions.fancytree("option", "disabled", false);
					$el.remove();
				});
			}
		});
	}
}
