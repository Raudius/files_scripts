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
	if (script) {
		name += script.id
	}

	return {
		name,
		displayName,
		mime: 'all',
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

/**
 * Registers the action handler for the multi select actions menu.
 *
 * @param {Function} action Callback to the handler
 * @param {Script|null} script the script which will be run as part of the action (or none specified)
 */
export function registerMenuOptions(action, script=null) {
	const actionObject = buildActionObject(action, script)
	if (OCA.Files && OCA.Files.App && OCA.Files.App.fileList) {
		OCA.Files.App.fileList.registerMultiSelectFileAction(actionObject)
	}

	if (OCA.Files && OCA.Files.fileActions) {
		OCA.Files.fileActions.registerAction(actionObject)
	}

	// Public share multiselect, wait two seconds to make sure the fileList is initialized
	setTimeout(() => {
		if (OCA.Sharing && OCA.Sharing.PublicApp && OCA.Sharing.PublicApp.fileList) {
			OCA.Sharing.PublicApp.fileList.registerMultiSelectFileAction(actionObject)
		}
	}, 2000)
}

