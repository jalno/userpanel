import "@jalno/translator";
import * as $ from 'jquery';
import {Main} from '../classes/Main';
import {Login} from '../classes/Login';
import {Register} from '../classes/Register';
import {Resetpwd} from '../classes/Resetpwd';
import {Users} from '../classes/Users';
import {Logs} from '../classes/Logs';
import Settings from '../classes/Settings';
import viewError from "../definitions/viewError";

export class View{
	public errors:viewError[] = [];
	public addError(error:viewError):void{
		this.errors.push(error);
	}
	public getErrorHTML():void{
		for(const error of this.errors){
			let alert:any = [];
			let data = error.getData();
			if(!(data instanceof Array)){
				data = [];
			}
			switch(error.getType()){
				case(error.FATAL):
					alert['type'] = 'danger';
					break;
				case(error.WARNING):
					alert['type'] = 'warning';
					break;
				case(error.NOTICE):
					alert['type'] = 'info';
					break;
			}
			if (alert["title"] === undefined) {
				alert['title'] = t(`error.${error.getType()}.title`);
			}
			const html = `
				<div class="row">
					<div class="col-xs-12">
						<div class="alert alert-block alert-${alert['type']}">
							<button data-dismiss="alert" class="close" type="button">×</button>
							<h4 class="alert-heading"><i class="fa fa-times-circle"></i> ${alert['title']}</h4>
							<p>${error.getMessage()}</p>
						</div>
					</div>
				</div>
			`;
			if(!$('.errors').length){
				$('.panel.panel-default').parents('.row').before('<div class="errors"></div>');
			}
			$('.errors').html(html);
		}
	}
}
$(function(){
	Main.init();
	Login.initIfNeeded();
	Register.initIfNeeded();
	Users.initIfNeeded();
	Settings.initIfNeeded();
	Resetpwd.initIfNeeded();
	Logs.initIfNeeded();
});