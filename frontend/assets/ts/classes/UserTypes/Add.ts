import "@jalno/translator";
import * as $ from "jquery";
import "jquery.fancytree";
declare const permissions: any; // permissions that put on page by dynamic data

export interface IUserpanelPermission {
	key: string;
	title?: string;
}
export interface IGroupPermissions {
	[key: string]: IUserpanelPermission[];
}
export type FancyTreeItemType = Array<{
	[key: string]: string | number | boolean,
}>;

export default class Add {
	public static initIfNeeded(): void {
		if (Add.$form.length) {
			Add.init();
		}
	}
	public static init(): void {
		const fancyTreeItems = Add.buildFancyTreeItems(Add.getGroupPermissions(permissions as IUserpanelPermission[]));
		Add.runFancyTree(fancyTreeItems);
		Add.setFancyTreeEvents();
		Add.submitFormListener();
	}

	protected static $form = $("body.usertypes.add-usertype");
	protected static $permissions = $(".panel-permissions", Add.$form);

	protected static runFancyTree(items: FancyTreeItemType[]) {
		Add.$permissions.fancytree({
			source: items,
			debugLevel: 0, // disabled
			rtl: $("body").hasClass("rtl"),
			clickFolderMode: 4, // activate_dblclick_expands
			selectMode: 3, // mutlti_hier
		});
	}

	protected static setFancyTreeEvents() {
		Add.$permissions.on("fancytreeclick", (event, data: Fancytree.EventData) => {
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
		$("form.add-usertypes", Add.$form).on("submit", () => {
			const selectedPermissions = getSelectedPermissions();
			console.info("all permissions:", permissions.length, permissions);
			console.info("selected permissions:", selectedPermissions.length, selectedPermissions);
			let html = "";
			for (const permission of selectedPermissions) {
				html += `<input type="hidden" name=permissions[] value="${permission}">`;
			}
			$(html).appendTo(Add.$permissions);

		});
	}
	private static getGroupPermissions(allPermissions: IUserpanelPermission[]) {
		const findBrothers = (needle: string, haystack: IUserpanelPermission[], finder: number = 0): IUserpanelPermission[] => {
			const brothers: IUserpanelPermission[] = [];
			for (const x of haystack) {
				const groupName = x.key.substr(0, finder) as string;
				if (groupName === needle) {
					brothers.push(x);
				}
			}
			return brothers;
		};
		const hasBrothers = (oPermissions: IUserpanelPermission[], ofinder: number) => {
			for (const op of oPermissions) {
				const index_ = op.key.indexOf("_", ofinder);
				const opName = op.key.substr(0, index_) as string;
				if (index_ !== -1 && opName.length) {
					let count = 0;
					for (const brotherPr of oPermissions) {
						const brotherPrName = brotherPr.key.substr(0, index_) as string;
						if (brotherPrName === opName && ++count > 1) {
							return true;
						}
					}
				}
			}
			return false;
		};
		const grouping = (iPermissions: IUserpanelPermission[], finder: number = 0) => {
			const grouped = new Object();
			for (const p of iPermissions) {
				const index_ = p.key.indexOf("_", finder);
				const groupName = p.key.substr(0, index_) as string;
				if (groupName.length && grouped[groupName] === undefined) {
					const brothers = findBrothers(groupName, iPermissions, index_);
					if (brothers.length > 1) {
						if (hasBrothers(brothers, (index_ + 1))) {
							grouped[groupName] = grouping(brothers, (index_ + 1));
							for (const nonBrother of brothers) {
								let xKey: string = nonBrother.key;
								const afterGroupName = nonBrother.key.substr(groupName.length + 1) as string;
								const nextKey = afterGroupName.indexOf("_");
								const nextPartAfterGroup = afterGroupName.substr(0, nextKey);
								if (nextKey !== -1 && nextPartAfterGroup.length) {
									xKey = groupName + "_" + nextPartAfterGroup;
								}
								if (grouped[groupName][xKey] === undefined && grouped[groupName][nonBrother.key] === undefined) {
									grouped[groupName][xKey] = nonBrother;
								}
							}
						} else {
							grouped[groupName] = brothers;
						}
					} else {
						grouped[p.key] = brothers;
					}
				}
			}
			return grouped;
		};
		return grouping(allPermissions);
	}
	private static translatePermission(permission: string, isTooltip: boolean = false): string {
		const key = "usertype.permissions." + permission + (isTooltip ? ".tooltip" : "");
		const translate = t(key);
		return (translate !== key ? translate : (isTooltip ? "" : key));
	}
	private static buildFancyTreeItems(groupPermissions: object) {
		// tslint:disable-next-line: ban-types
		const isUserpanelPermission = (object: Object) => {
			return object.hasOwnProperty("key");
		};
		const items: FancyTreeItemType[] = [];
		for (const index in groupPermissions) {
			if (groupPermissions[index] !== undefined) {
				if (isUserpanelPermission(groupPermissions[index])) {
					const permission = groupPermissions[index] as IUserpanelPermission;
					items.push({
						key: permission.key,
						selected: false,
						title: Add.translatePermission(permission.key),
						tooltip: Add.translatePermission(permission.key, true),
						icon: "/packages/userpanel/frontend/assets/images/key_1.png",
						folder: false,
						checkbox: true,
					});
				} else {
					items.push({
						key: index,
						selected: false,
						title: Add.translatePermission(index),
						tooltip: Add.translatePermission(index, true),
						icon: true,
						folder: true,
						checkbox: true,
						children: Add.buildFancyTreeItems(groupPermissions[index]),
					});
				}
			}
		}
		return items;
	}
}
