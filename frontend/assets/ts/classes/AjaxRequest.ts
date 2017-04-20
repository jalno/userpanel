/// <reference types="jquery"/>

import * as $ from "jquery";
import {webuilder, Router} from "webuilder";
export interface AjaxSettings{
    accepts?: any;
    async?: boolean;
    beforeSend? (jqXHR: JQueryXHR, settings: JQueryAjaxSettings): any;
   	cache?: boolean;
    complete? (jqXHR: JQueryXHR, textStatus: string): any;
    contents?: { [key: string]: any; };
    contentType?: any;
    context?: any;
    converters?: { [key: string]: any; };
    crossDomain?: boolean;
    data?: any;
    dataFilter? (data: any, ty: any): any;
    dataType?: string;
    error? (error:webuilder.AjaxError, jqXHR: JQueryXHR):void;
    global?: boolean;
    headers?: { [key: string]: any; };
    isLocal?: boolean;
    jsonp?: any;
    jsonpCallback?: any;
    method?: string;
    mimeType?: string;
    password?: string;
    processData?: boolean;
    scriptCharset?: string;
    statusCode?: { [key: string]: any; };
    success? (data: webuilder.AjaxResponse, textStatus: string, jqXHR: JQueryXHR): any;
    timeout?: number;
    traditional?: boolean;
    type?: string;
    url?: string;
    username?: string;
    xhr?: any;
    xhrFields?: { [key: string]: any; };
}
export function AjaxRequest(settings: AjaxSettings ){
	let newSettings:JQueryAjaxSettings = {};
	for(let key in settings){
		if(key != 'success' && key != 'error'){
			newSettings[key] = settings[key];
		}
	}
	newSettings.success = (data:webuilder.AjaxResponse, textStatus, JqXHR) => {
		if(data.status){
			if(settings.hasOwnProperty('success')){
				settings.success(data, textStatus,JqXHR);
			}
		}else{
			if(data.hasOwnProperty('error')){
				data.error.forEach((error) => {
					if(settings.hasOwnProperty('error')){
						settings.error(error, JqXHR);
					}
				});
			}else{
				if(settings.hasOwnProperty('error')){
					let error:webuilder.AjaxError = {
						type:"fatal",
						error:"unknown"
					}
					settings.error(error, JqXHR);
				}
			}
		}
	}
	newSettings.error = (JqXHR, textStatus) => {
		if(settings.hasOwnProperty('error')){
			let error:webuilder.AjaxError = {
				type:"fatal",
				error:textStatus
			}
			settings.error(error, JqXHR);
		}
	}
	return $.ajax(newSettings);
}