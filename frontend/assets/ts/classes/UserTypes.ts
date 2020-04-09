import * as $ from "jquery";
import Add from "./UserTypes/Add";
import Edit from "./UserTypes/Edit";

export default class UserTypes {
	public static initIfNeeded(): void {
		UserTypes.init();
	}
	public static init(): void {
		Add.initIfNeeded();
		Edit.initIfNeeded();
	}
}
