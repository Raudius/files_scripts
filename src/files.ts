import { translate as t } from './l10n'
import {ActionContext, type IFileAction, Node, registerFileAction} from '@nextcloud/files'
import {Script, scriptAllowedForNodes} from "./types/script";
import FileCog from '@mdi/svg/svg/file-cog.svg';
import {emit} from "@nextcloud/event-bus"


export function reloadDirectory(node: Node){
	node && emit("files:node:updated", node)

	// Legacy reload for public shares
	const fileList = OCA?.Sharing?.PublicApp?.fileList
	if (!fileList) {
		return
	}
	fileList.changeDirectory(fileList.getCurrentDirectory(), true, true)
}

export type HandlerFunc = (files: ActionContext) => void;


function buildActionObject(myHandler: HandlerFunc, script: Script|null = null): IFileAction {
	const displayName = script ? script.title : t('files_scripts', 'More actions')
	const id = 'files_scripts_action' + (script ? script.id : "")
	const order = 1000 + (script ? 0 : 1)


	return {
		id: id,
		displayName: (_): string => displayName,
		title: (_): string => displayName,
		iconSvgInline: (_): string => FileCog,
		enabled: (ctx: ActionContext) => {
			return script === null || scriptAllowedForNodes(script, ctx.nodes as Node[])
		},
		order: order,
		async exec(ctx) {
			myHandler(ctx)
			return null
		},
		async execBatch(ctx) {
			myHandler(ctx)
			return null
		},
	} as IFileAction
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

function registerPublicSharingFileAction(action: IFileAction, script: Script) {
	if (!OCA.Sharing || !OCA.Sharing.PublicApp || !OCA.Sharing.PublicApp.fileList) {
		return
	}
	// TODO fix register public file sharing
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

