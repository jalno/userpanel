import "@jalno/translator";
import * as $ from "jquery";
import "jquery.fancytree";
declare const permissions: any; // permissions that put on page by dynamic data

export interface IUserpanelPermission {
	key: string;
	title?: string;
	value: boolean;
}
export interface IGroupPermissions {
	[key: string]: IUserpanelPermission[];
}
export type FancyTreeItemType = Array<{
	[key: string]: string | number | boolean,
}>;

export default class Edit {
	public static initIfNeeded(): void {
		if (Edit.$form.length) {
			Edit.init();
		}
	}
	public static init(): void {
		const fancyTreeItems = Edit.buildFancyTreeItems(Edit.getGroupPermissions(permissions as IUserpanelPermission[]));
		Edit.runFancyTree(fancyTreeItems.items);
		Edit.setFancyTreeEvents();
		Edit.submitFormListener();
	}

	protected static $form = $("body.usertypes.edit-usertype");
	protected static $permissions = $(".panel-permissions", Edit.$form);

	protected static runFancyTree(items: FancyTreeItemType[]) {
		Edit.$permissions.fancytree({
			source: items,
			debugLevel: 0, // disabled
			rtl: $("body").hasClass("rtl"),
			clickFolderMode: 4, // activate_dblclick_expands
			selectMode: 3, // mutlti_hier
		});
	}

	protected static setFancyTreeEvents() {
		Edit.$permissions.on("fancytreeclick", (event, data: Fancytree.EventData) => {
			const $target = $(data.originalEvent.target);
			if ($target.hasClass("fancytree-checkbox") || $target.hasClass("fancytree-expander")) {
				return;
			}
			if (data.node.isFolder()) {
				if (data.node.isSelected()) {
					if (data.node.isExpanded()) {
						data.node.setSelected(false);
					}
				} else {
					let hasSelected = false;
					for (const child of data.node.children) {
						if (child.isSelected()) {
							hasSelected = true;
							break;
						}
					}
					if (hasSelected) {
						data.node.setSelected();
					}
				}
			} else {
				data.node.toggleSelected();
			}
		});
	}
	protected static submitFormListener() {
		const getSelectedPermissions = () => {
			const selectedPermissions: string[] = [];
			const tree = this.$permissions.fancytree("getTree") as Fancytree.Fancytree;
			const selectedNodes = tree.getSelectedNodes();
			for (const node of selectedNodes) {
				if (node.isFolder()) {
					continue;
				}
				selectedPermissions.push(node.key);
			}
			return selectedPermissions;
		};
		$("form.edit-usertype", Edit.$form).on("submit", () => {
			let html = "";
			for (const permission of getSelectedPermissions()) {
				html += `<input type="hidden" name=permissions[] value="${permission}">`;
			}
			$(html).appendTo(Edit.$permissions);

		});
	}
	private static getGroupPermissions(allPermissions: IUserpanelPermission[]) {
		const getGroupPermissions = (inputPermissions: IUserpanelPermission[], finder: number = 0) => {
			const hasBrothers = (otherPermissions: IUserpanelPermission[], ofinder: number) => {
				for (const pr of otherPermissions) {
					const prIndex = pr.key.indexOf("_", ofinder);
					const prName = pr.key.substr(0, prIndex) as string;
					if (prName.length) {
						const brotherPermissions: IUserpanelPermission[] = [];
						for (const brotherPr of otherPermissions) {
							const brotherPrName = brotherPr.key.substr(0, prIndex) as string;
							if (brotherPrName === prName) {
								brotherPermissions.push(brotherPr);
							}
						}
						if (brotherPermissions.length > 1) {
							return true;
						}
					}
				}
				return false;
			};
			const groupPermissions = new Object();
			for (const permission of inputPermissions) {
				const firstIndex = permission.key.indexOf("_", finder);
				const groupName = permission.key.substr(0, firstIndex) as string;
				if (groupName.length) {
					if (groupPermissions[groupName] === undefined) {
						const brotherPermissions: IUserpanelPermission[] = [];
						for (const permission1 of inputPermissions) {
							const groupName1 = permission1.key.substr(0, firstIndex) as string;
							if (groupName1 === groupName) {
								brotherPermissions.push(permission1);
							}
						}
						if (brotherPermissions.length > 1) {
							if (hasBrothers(brotherPermissions, firstIndex + 1)) {
								const childs = getGroupPermissions(brotherPermissions, (firstIndex + 1));
								groupPermissions[groupName] = childs;
								for (const notBrotherPermission of brotherPermissions) {
									let needAdd = true;
									for (const child in childs) {
										if (childs[child] !== undefined) {
											if (notBrotherPermission.key.substr(0, child.length) === child) {
												needAdd = false;
											}
										}
									}
									if (needAdd) {
										groupPermissions[groupName][notBrotherPermission.key] = notBrotherPermission;
									}
								}
							} else {
								groupPermissions[groupName] = brotherPermissions;
							}
						} else {
							groupPermissions[permission.key] = brotherPermissions;
						}
					}
				}
			}
			return groupPermissions;
		};
		return getGroupPermissions(allPermissions as IUserpanelPermission[]);
	}
	private static translatePermission(key: string) {
		const translate = t("usertype.permissions." + key);
		return (translate.length ? translate : key);
	}
	private static buildFancyTreeItems(groupPermissions: object) {
		// tslint:disable-next-line: ban-types
		const isUserpanelPermission = (object: Object) => {
			return object.hasOwnProperty("key") && object.hasOwnProperty("value");
		};
		const items: FancyTreeItemType[] = [];
		let selected: boolean = true;
		for (const index in groupPermissions) {
			if (groupPermissions[index] !== undefined) {
				if (isUserpanelPermission(groupPermissions[index])) {
					const permission = groupPermissions[index] as IUserpanelPermission;
					items.push({
						key: permission.key,
						selected: permission.value,
						title: Edit.translatePermission(permission.key),
						icon: true,
						folder: false,
						checkbox: true,
					});
					if (!permission.key) {
						selected = false;
					}
				} else {
					const childs = Edit.buildFancyTreeItems(groupPermissions[index]);
					items.push({
						key: index,
						selected: childs.selected,
						title: Edit.translatePermission(index),
						icon: true,
						folder: true,
						checkbox: true,
						children: childs.items,
					});
				}
			}
		}
		return {
			items,
			selected,
		};
	}
}
