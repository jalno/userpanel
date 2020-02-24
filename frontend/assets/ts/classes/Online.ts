
import { AjaxRequest } from "webuilder";

export interface IOnlineOptions {
	period?: number;
}

export default class Online {
	public static update(options: IOnlineOptions) {
		let hasUpdate = false;
		if (options.hasOwnProperty("period") && options.period > 0 && options.period !== Online.options.period) {
			Online.options.period = options.period;
			hasUpdate = true;
		}
		if (hasUpdate) {
			Online.createInterval();
		}
	}
	public static run() {
		Online.createInterval();
	}
	private static options = {
		period: 15000,
		data: {
			ajax: 1,
		}
	}
	private static interval: number;
	private static createInterval() {
		if (Online.interval !== undefined) {
			clearInterval(Online.interval);
			Online.interval = undefined;
		}
		console.log("Online.options.period", Online.options.period);
		Online.interval = setInterval(() => {
            AjaxRequest({
                url: "userpanel/online",
                cache: false,
				data: Online.options.data,
				success: (response) => {
					$(window).trigger("packages.userpanel.online.response", [response]);	
				},
            });
		}, Online.options.period);
	}
}
