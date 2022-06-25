import {translate as t} from "./l10n";

export function registerMultiSelect(action) {
	const actionObj = {
		name: 'files_scripts_multi_action',
		displayName: t('More actions'),
		iconClass: 'icon-files_scripts',
		order: 1001,
		action
	}

	if (OCA.Files.App.fileList) {
		OCA.Files.App.fileList.registerMultiSelectFileAction(actionObj)
	} else {
		OC.Plugins.register('OCA.Files.FileList', {
			attach(fileList) {
				fileList.registerMultiSelectFileAction(actionObj)
			}
		})
	}
}

export function registerFileSelect(actionHandler) {
	OCA.Files.fileActions.registerAction({
		name: "files_scripts_action",
		displayName: t('More actions'),
		mime: 'all',
		permissions: OC.PERMISSION_READ,
		iconClass: 'icon-files_scripts',
		actionHandler
	})
}
