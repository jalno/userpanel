import * as $ from "jquery";
import "jquery-validation";
import AutoComplete from "./AutoComplete";
import { webuilder } from "webuilder";

export class Logs{
	private static $form;
	private static runSearchUserAutoComplete(){
		let ac = new AutoComplete($("input[name=user_name]", this.$form));
		ac.users();
	}
	public static init():void {
		const $body = $('body');
		if($body.hasClass("users-logs")){
			this.$form = $("#userLogsSearch");
			this.runSearchUserAutoComplete();
		}
	}
	public static initIfNeeded():void{
		if($('body').hasClass('users-logs')){
			this.init();
		}
	}
}
