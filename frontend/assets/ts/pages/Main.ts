import * as $ from 'jquery';
import {Main} from '../classes/Main';
import {Login} from '../classes/Login';
import {Register} from '../classes/Register';
import {Resetpwd} from '../classes/Resetpwd';
import {Users} from '../classes/Users';
import {Logs} from '../classes/Logs';
import viewError from "../definitions/viewError";
import Apps from "../classes/Settings/Apps";
import Apis from "../classes/Settings/Apis";

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
					if(!alert['title']){
						alert['title'] = 'خطا';
					}
					break;
				case(error.WARNING):
					alert['type'] = 'warning';
					if(!alert['title']){
						alert['title'] = 'اخطار';
					}
					break;
				case(error.NOTICE):
					alert['type'] = 'info';
					if(!alert['title']){
						alert['title'] = 'توجه';
					}
					break;
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
	Resetpwd.initIfNeeded();
	Logs.initIfNeeded();
	Apps.initIfNeeded();
	Apis.initIfNeeded();
});