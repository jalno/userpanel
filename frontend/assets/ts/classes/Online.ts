import { AjaxRequest, Options, Router } from "webuilder";

export default class Online {
	public static run() {
		let period = Options.get("packages.userpanel.online.period");
		if (!period) {
			period = Online.options.period;
		}
		Online.interval = setInterval(() => {
			AjaxRequest({
				url: "userpanel/online",
				cache: false,
				data: Online.options.data,
				success: (response) => {
					$(window).trigger("packages.userpanel.online.response", [response]);
				},
				error: (error) => {
					if (typeof(error.redirect) != "undefined") {
						window.location.href = Router.url(error.redirect, {error_message : error.message} as any);
					}
				}
			});
			if (period !== Options.get("packages.userpanel.online.period")) {
				clearInterval(Online.interval);
				Online.interval = undefined;
				Online.run();
			}
		}, period);
	}
	private static interval: number;
	private static options = {
		period: 15000,
		data: {
			ajax: 1,
		},
	};
}
