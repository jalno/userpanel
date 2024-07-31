import "@jalno/translator";
import "jquery.fancytree";
import Permissions, { IFancyTreeItemType, IUserpanelPermission } from "../Permissions";
import UserTypes from "../UserTypes";
declare const usertypePermissions: IUserpanelPermission[]; // permissions that put on page by dynamic data

export default class Add {
	public static initIfNeeded(): void {

		Add.$form = $("body.usertypes.add-usertype form.add-usertypes");

		if (Add.$form.length) {
			Add.init();
		}
	}
	public static init(): void {

		Add.$permissions = $(".panel-permissions", Add.$form);

		UserTypes.runFancyTree(Add.$permissions, Permissions.buildFancyTreeItems(usertypePermissions));
		Permissions.setFancyTreeEvents(Add.$permissions);
		UserTypes.runsubmitFormListener(Add.$form, Add.$permissions);

		const $select = $('select[name="children-type"]', Add.$form);

		if ($select.length) {
			UserTypes.runCopyFromAnothoerUsertypeListener($select, Add.$permissions);
		}
	}

	protected static $form: JQuery;
	protected static $permissions: JQuery;
}
