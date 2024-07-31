import "@jalno/translator";
import {Login} from "../classes/Login";
import {Logs} from "../classes/Logs";
import {Main} from "../classes/Main";
import {Register} from "../classes/Register";
import {Resetpwd} from "../classes/Resetpwd";
import Settings from "../classes/Settings";
import {Users} from "../classes/Users";
import UserTypes from "../classes/UserTypes";
import ViewError from "../definitions/ViewError";

export class View {
	public errors: ViewError[] = [];

	public addError(error: ViewError): void {
		this.errors.push(error);
	}
	public getErrorHTML(): void {
		for (const error of this.errors) {
			const alert: any = [];
			let data = error.getData();
			if (!(data instanceof Array)) {
				data = [];
			}
			switch (error.getType()) {
				case(error.FATAL):
					alert.type = "danger";
					break;
				case(error.WARNING):
					alert.type = "warning";
					break;
				case(error.NOTICE):
					alert.type = "info";
					break;
			}
			if (alert.title === undefined) {
				alert.title = t(`error.${error.getType()}.title`);
			}
			const html = `
				<div class="row">
					<div class="col-xs-12">
						<div class="alert alert-block alert-${alert.type}">
							<button data-dismiss="alert" class="close" type="button">×</button>
							<h4 class="alert-heading"><i class="fa fa-times-circle"></i> ${alert.title}</h4>
							<p>${error.getMessage()}</p>
						</div>
					</div>
				</div>
			`;
			if (!$(".errors").length) {
				$(".panel.panel-default").parents(".row").before('<div class="errors"></div>');
			}
			$(".errors").html(html);
		}
	}
}
$(() => {
	Main.init();
	Login.initIfNeeded();
	Register.initIfNeeded();
	Users.initIfNeeded();
	UserTypes.initIfNeeded();
	Settings.initIfNeeded();
	Resetpwd.initIfNeeded();
	Logs.initIfNeeded();
});
