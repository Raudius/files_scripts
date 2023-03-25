import { translate as t } from './l10n'

export function reloadCurrentDirectory() {
	const fileList = OCA.Files?.App?.fileList ?? OCA?.Sharing?.PublicApp?.fileList
	if (!fileList) {
		return
	}
	fileList.changeDirectory(fileList.getCurrentDirectory(), true, true)
}

function buildActionObject(myHandler, script=null) {
	const displayName = script ? script.title : t('files_scripts', 'More actions')
	let name = 'files_scripts_action'
	let mime = 'all'
	if (script) {
		name += script.id
		mime = script.mimetype ? script.mimetype : 'all'
	}

	return {
		name,
		displayName,
		mime,
		mimetype: mime,
		permissions: OC.PERMISSION_READ,
		order: 1001,
		iconClass: 'icon-files_scripts',

		// For multi-file picker
		action: (files) => {
			myHandler(files, script)
		},
		// For single-file picker
		actionHandler: (filePath, context) => {
			const file = context?.fileInfoModel?.attributes
			file && myHandler([file], script)
		},
	}
}

export function registerSingleMenuOptions(handler, script=null) {
	const actionObject = buildActionObject(handler, script)
	if (OCA.Files && OCA.Files.fileActions) {
		OCA.Files.fileActions.registerAction(actionObject)
	}
}

export function registerMultiMenuOptions(handler) {
	const actionObject = buildActionObject(handler)

	// Public share multiselect, wait two seconds to make sure the fileList is initialized
	setTimeout(() => {
		if (OCA.Files && OCA.Files.App && OCA.Files.App.fileList) {
			OCA.Files.App.fileList.registerMultiSelectFileAction(actionObject)
		}

		if (OCA.Sharing && OCA.Sharing.PublicApp && OCA.Sharing.PublicApp.fileList) {
			OCA.Sharing.PublicApp.fileList.registerMultiSelectFileAction(actionObject)
		}
	}, 2000)
}
