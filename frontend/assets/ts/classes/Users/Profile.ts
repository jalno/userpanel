import Edit from "./Profile/Edit";
import Settings from "./Profile/Settings";

export default class Profile {

	public static initIfNeeded(): void {
		Edit.initIfNeeded();
		Settings.initIfNeeded();
	}
}
