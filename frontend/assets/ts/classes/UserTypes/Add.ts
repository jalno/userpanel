import "@jalno/translator";
import * as $ from "jquery";
import "jquery.fancytree";
import Permissions, { IFancyTreeItemType, IUserpanelPermission } from "../Permissions";
declare const usertypePermissions: IUserpanelPermission[]; // permissions that put on page by dynamic data

export default class Add {
	public static initIfNeeded(): void {
		if (Add.$form.length) {
			Add.init();
		}
	}
	public static init(): void {
		Add.runFancyTree(Permissions.buildFancyTreeItems(usertypePermissions));
		Permissions.setFancyTreeEvents(Add.$permissions);
		Add.submitFormListener();
	}

	protected static $form = $("body.usertypes.add-usertype form.add-usertypes");
	protected static $permissions = $(".panel-permissions", Add.$form);

	protected static runFancyTree(items: IFancyTreeItemType[]) {
		Add.$permissions.fancytree({
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
	protected static submitFormListener() {
		Add.$form.on("submit", () => {
			const selectedPermissions = Permissions.getSelectedPermissionsFromFancytree(this.$permissions);
			console.info("all permissions:", usertypePermissions.length, usertypePermissions);
			console.info("selected permissions:", selectedPermissions.length, selectedPermissions);
			let html = "";
			for (const permission of selectedPermissions) {
				html += `<input type="hidden" name=permissions[] value="${permission}">`;
			}
			$(html).appendTo(Add.$permissions);
		});
	}
}
