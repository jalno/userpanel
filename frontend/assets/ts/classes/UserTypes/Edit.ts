import "@jalno/translator";
import "jquery.fancytree";
import Permissions, { IUserpanelPermission } from "../Permissions";
import UserTypes from "../UserTypes";

declare const usertypePermissions: IUserpanelPermission[]; // permissions that put on page by dynamic data

export default class Edit {
	public static initIfNeeded(): void {

		Edit.$form = $("body.usertypes.edit-usertype form.edit-usertype");

		if (Edit.$form.length) {
			Edit.init();
		}
	}
	public static init(): void {

		Edit.$permissions = $(".panel-permissions", Edit.$form);

		UserTypes.runFancyTree(Edit.$permissions, Permissions.buildFancyTreeItems(usertypePermissions));
		Permissions.setFancyTreeEvents(Edit.$permissions);
		UserTypes.runsubmitFormListener(Edit.$form, Edit.$permissions);
	}

	protected static $form: JQuery;
	protected static $permissions: JQuery;
}
