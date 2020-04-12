import * as $ from "jquery";

export default class ViewError {
	public WARNING = "warning";
	public FATAL = "fatal";
	public NOTICE = "notice";
	protected code: string;
	protected data: any;
	protected message: string;
	protected type: string = this.FATAL;

	public setCode(code: string) {
		this.code = code;
	}
	public getCode(): string {
		return this.code;
	}
	public setData(val: any, key: string = null) {
		if (key) {
			this.data[key] = val;
		} else {
			this.data = val;
		}
	}
	public getData(key: string = null) {
		if (key) {
			return(key in this.data  ? this.data[key] : null);
		} else {
			return this.data;
		}
	}
	public setType(type: string) {
		if ([this.WARNING, this.FATAL,this.NOTICE].indexOf(type) > -1) {
			this.type = type;
		}
	}
	public getType(): string {
		return this.type;
	}
	public setMessage(message: string) {
		this.message = message;
	}
	public getMessage(): string {
		return this.message;
	}
}
