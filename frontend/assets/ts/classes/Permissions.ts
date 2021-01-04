import "@jalno/translator";
import * as $ from "jquery";
import "jquery.fancytree";

export interface IUserpanelPermission {
	key: string;
	title?: string;
	value?: boolean;
}

export interface IFancyTreeItemType {
	children: IFancyTreeItemType[];
	key: string;
	[key: string]: string | number | boolean | IFancyTreeItemType[];
}

export default class Permissions {

	public static buildFancyTreeItems(allPermissions: IUserpanelPermission[]): IFancyTreeItemType[] {
		allPermissions.sort();
		const tree: IFancyTreeItemType = {
			key: undefined,
			children: [],
		};
		const insertNode = (parent: IFancyTreeItemType, permission: IUserpanelPermission): void => {
			let common: string[] = [];
			const selfParts = permission.key.substr(parent.key ? parent.key.length + 1 : 0).split("_");
			for (const child of parent.children) {
				const childCommon = [];
				const parts = child.key.substr(parent.key ? parent.key.length + 1 : 0).split("_");
				for (let x = 0, l = Math.min(selfParts.length, parts.length); x < l; x++) {
					if (parts[x] === selfParts[x]) {
						childCommon.push(parts[x]);
					} else {
						break;
					}
				}
				if (childCommon.length > common.length) {
					common = childCommon;
				}
			}
			if (!common.length) {
				parent.children.push({
					children: [],
					folder: false,
					checkbox: true,
					key: permission.key,
					title: this.translatePermission(permission.key),
					tooltip: this.translatePermission(permission.key, true),
					selected: permission.value || false,
				});
				return;
			}
			const key = (parent.key ? parent.key + "_" : "") + common.join("_");
			let newNode: IFancyTreeItemType = {
				key: key,
				folder: true,
				children: [],
				checkbox: true,
				title: this.translatePermission(key),
				tooltip: this.translatePermission(key, true),
			};
			let replaced = false;
			for (let x = 0, l = parent.children.length; x < l; x++) {
				const child = parent.children[x];
				if (child.key === newNode.key) {
					if (child.folder === true) {
						newNode = child;
						replaced = true;
					} else {
						newNode.children.push(child);
						parent.children.splice(x, 1);
					}
					break;
				} else if (child.key.substr(0, newNode.key.length + 1) === newNode.key + "_") {
					newNode.children.push(child);
					parent.children.splice(x, 1);
					break;
				}
			}
			if (!replaced) {
				parent.children.unshift(newNode);
			}
			insertNode(newNode, permission);
		};
		for (const permission of allPermissions) {
			insertNode(tree, permission);
		}
		return tree.children;
	}

	public static setFancyTreeEvents($container: JQuery) {
		$container.on("fancytreeclick", (_event, data: Fancytree.EventData) => {
			const $target = $(data.originalEvent.target);
			/* prevent handle fancytree default behavior */
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

	public static getSelectedPermissionsFromFancytree($container: JQuery): string[] {
		const selectedPermissions: string[] = [];
		const tree = $container.fancytree("getTree") as Fancytree.Fancytree;
		const selectedNodes = tree.getSelectedNodes();
		for (const node of selectedNodes) {
			if (node.isFolder()) {
				continue;
			}
			selectedPermissions.push(node.key);
		}
		return selectedPermissions;
	}

	private static translatePermission(permission: string, isTooltip: boolean = false): string {
		const key = "usertype.permissions." + permission + (isTooltip ? ".tooltip" : "");
		const translated = t(key);
		if (translated !== key) {
			return translated;
		}
		if (!isTooltip) {
			return key;
		}
		return "";
	}
}
