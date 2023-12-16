import { translate as t } from './l10n'
import {FileAction, Node, FileType, DefaultType, registerFileAction} from '@nextcloud/files'
import {Script} from "./types/script";
import FileCog from '@mdi/svg/svg/file-cog.svg';


export const OpenFileAction = new FileAction({
	id: 'open-in-files-recent',
	displayName: () => t('files', 'Open in Files'),
	iconSvgInline: () => '',

	enabled: (nodes, view) => view.id === 'recent',

	async exec(node: Node) {
		let dir = node.dirname
		if (node.type === FileType.Folder) {
			dir = dir + '/' + node.basename
		}

		return null
	},

	// Before openFolderAction
	order: -1000,
	default: DefaultType.HIDDEN,
})


export function reloadCurrentDirectory(node: Node) {
	OpenFileAction.exec(node, null, null)
}

export type HandlerFunc = (files: Node[]) => void;

function buildActionObject(myHandler: HandlerFunc, script: Script|null = null): FileAction {
	const displayName = script ? script.title : t('files_scripts', 'More actions')
	let name = 'files_scripts_action'
	let mime = 'all'
	if (script) {
		name += script.id
		mime = script.mimetype ? script.mimetype : 'all'
	}

	return {
		id: name,
		displayName: (files, view): string => displayName,
		title: (files, view): string => displayName,
		iconSvgInline: (files, view): string => FileCog,
		enabled: (files, view) => {
			if (mime === "all") {
				return true
			}

			return files.some(f => {
				return f.mime === mime
			})
		},
		order: 1001,
		exec(file, view, dir) {
			return new Promise<null>((resolve) => {
				myHandler([file])
				resolve(null)
			})
		},
		execBatch(files, view, dir) {
			return new Promise<boolean[]>((resolve) => {
				myHandler(files)
				return resolve([])
			})
		},
	} as FileAction
}

export function registerMenuOption(handler: HandlerFunc, script=null) {
	const actionObject = buildActionObject(handler, script)
	registerFileAction(actionObject)
}

