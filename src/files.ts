import { translate as t } from './l10n'
import {FileAction, Node, registerFileAction, View} from '@nextcloud/files'
import {Script, scriptAllowedForNodes} from "./types/script";
import FileCog from '@mdi/svg/svg/file-cog.svg';
import {emit} from "@nextcloud/event-bus"
import {NodeInfo} from "./types/files";


export function reloadDirectory(node: Node){
	node && emit("files:node:updated", node)

	// Legacy reload for public shares
	const fileList = OCA?.Sharing?.PublicApp?.fileList
	if (!fileList) {
		return
	}
	fileList.changeDirectory(fileList.getCurrentDirectory(), true, true)
}

export type HandlerFunc = (files: NodeInfo[], currentFolder) => void;

function getNodeInfo(node: Node): NodeInfo {
	return {
		id: node.fileid,
		basename: node.basename,
		fullPath: node.path,
		mime: node.mime
	} as NodeInfo
}

function buildActionObject(myHandler: HandlerFunc, script: Script|null = null): FileAction {
	const displayName = script ? script.title : t('files_scripts', 'More actions')
	const id = 'files_scripts_action' + (script ? script.id : "")
	const order = 1000 + (script ? 0 : 1)

	const handleExec = (handler: HandlerFunc, nodes: Node[], view: View, dir: string): void => {
		const nodeInfos = nodes.map(getNodeInfo)

		if (!view) {
			handler(nodeInfos, null)
		} else {
			view.getContents(dir).then(function (value) {
				myHandler(nodeInfos, value.folder)
			})
		}
	}

	return new FileAction({
		id: id,
		displayName: (files, view): string => displayName,
		title: (files, view): string => displayName,
		iconSvgInline: (files, view): string => FileCog,
		enabled: (files, view) => {
			return script === null || scriptAllowedForNodes(script, files)
		},
		order: order,
		async exec(file, view, dir) {
			handleExec(myHandler, [file], view, dir)
			return null
		},
		async execBatch(files, view, dir) {
			handleExec(myHandler, files, view, dir)
			return [null]
		},
	})
}

function nodeFromLegacy(file: any): Node {
	return {
		get fileid(): number | undefined {
			return file.id
		},
		get basename(): string {
			return file.name
		},
		get path(): string {
			return file.path
		}
	} as Node
}

function registerPublicSharingFileAction(action: FileAction, script: Script) {
	if (!OCA.Sharing || !OCA.Sharing.PublicApp || !OCA.Sharing.PublicApp.fileList) {
		return
	}

	let mimes = ["all"]
	if (script) {
		mimes = script.fileTypes
	}

	for (const mime of mimes) {
		const legacyAction = {
			name: action.id,
			displayName: action.displayName(null, null),
			mime: mime,
			mimetype: mime,
			permissions: OC.PERMISSION_READ,
			order: 1001,
			iconClass: 'icon-files_scripts',

			// For multi-file picker
			action: (files: any[]) => {
				const nodes = files.map(nodeFromLegacy)
				action.execBatch(nodes, null, null)
			},
			// For single-file picker
			actionHandler: (filePath, context) => {
				const file = context?.fileInfoModel?.attributes
				if (!file) {
					console.log("[files_scripts] Failed to find file for action handler.")
					return
				}

				const node = nodeFromLegacy(file)
				action.exec(node, null, null)
			},
		}

		OCA.Sharing.PublicApp.fileList.registerMultiSelectFileAction(legacyAction)
		OCA.Sharing.PublicApp.fileList.fileActions.registerAction(legacyAction)
	}
}

export function registerMenuOption(handler: HandlerFunc, script=null) {
	const actionObject = buildActionObject(handler, script)
	registerFileAction(actionObject)

	// FIXME: 	Public shares are still using the legacy file viewer, if it ever updates to the Vue version
	//  		we should update this code.
	setTimeout(() => {
		registerPublicSharingFileAction(actionObject, script)
	}, 2000)
}

