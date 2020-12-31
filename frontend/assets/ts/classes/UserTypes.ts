import * as $ from "jquery";
import Add from "./UserTypes/Add";
import Edit from "./UserTypes/Edit";
import { AjaxRequest, Router, webuilder } from "webuilder";

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
}
