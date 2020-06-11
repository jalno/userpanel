import "@jalno/translator";
import * as $ from "jquery";
import "jquery.fancytree";
declare const permissions: any; // permissions that put on page by dynamic data

export interface IUserpanelPermission {
	key: string;
	title?: string;
	value: boolean;
}
export interface IFancyTreeItemType {
	children: IFancyTreeItemType[];
	key: string;
	[key: string]: string | number | boolean | IFancyTreeItemType[];
}

export default class Edit {
	public static initIfNeeded(): void {
		if (Edit.$form.length) {
			Edit.init();
		}
	}
	public static init(): void {
		Edit.runFancyTree(Edit.buildFancyTreeItems(permissions as IUserpanelPermission[]));
		Edit.setFancyTreeEvents();
		Edit.submitFormListener();
	}

	protected static $form = $("body.usertypes.edit-usertype form.edit-usertype");
	protected static $permissions = $(".panel-permissions", Edit.$form);

	protected static runFancyTree(items: IFancyTreeItemType[]) {
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
		Edit.$form.on("submit", () => {
			const selectedPermissions = getSelectedPermissions();
			console.info("all permissions:", permissions.length, permissions);
			console.info("selected permissions:", selectedPermissions.length, selectedPermissions);
			let html = "";
			for (const permission of selectedPermissions) {
				html += `<input type="hidden" name=permissions[] value="${permission}">`;
			}
			$(html).appendTo(Edit.$permissions);

		});
	}
	private static buildFancyTreeItems(allPermissions: IUserpanelPermission[]) {
		allPermissions.sort();
		const tree: IFancyTreeItemType = {
			key: undefined,
			children: [],
		};
		const insertNode = (parent: IFancyTreeItemType, permission: IUserpanelPermission) => {
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
					key: permission.key,
					selected: permission.value,
					children: [],
					title: Edit.translatePermission(permission.key),
					tooltip: Edit.translatePermission(permission.key, true),
					icon: "/packages/userpanel/frontend/assets/images/key_1.png",
					folder: false,
					checkbox: true,
				});
				return;
			}
			const key = (parent.key ? parent.key + "_" : "") + common.join("_");
			let newNode: IFancyTreeItemType = {
				key: key,
				title: Edit.translatePermission(key),
				tooltip: Edit.translatePermission(key, true),
				children: [],
				folder: true,
				checkbox: true,
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
	private static translatePermission(permission: string, isTooltip: boolean = false): string {
		const key = "usertype.permissions." + permission + (isTooltip ? ".tooltip" : "");
		const translate = t(key);
		return (translate !== key ? translate : (isTooltip ? "" : key));
	}
}
