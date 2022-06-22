import {translate as t} from "./l10n";

export function registerMultiSelect(action) {
	const FilesPlugin = {
		attach(fileList) {
			fileList.registerMultiSelectFileAction({
				name: 'files_actions',
				displayName: t('Run action'),
				iconClass: 'icon-files_scripts',
				order: 1001,
				action
			})
		}
	}

	OC.Plugins.register('OCA.Files.FileList', FilesPlugin)
}

export function registerFileSelect(actionHandler) {
	OCA.Files.fileActions.registerAction({
		name: "files_scripts_action",
		displayName: t('Run action'),
		mime: 'all',
		permissions: OC.PERMISSION_READ,
		iconClass: 'icon-files_scripts',
		actionHandler
	})
}
