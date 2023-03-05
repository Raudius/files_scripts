import { translate as t } from './l10n'

/**
 * Registers the action handler for the multi select actions menu.
 *
 * @param {Function} action Callback to the handler
 */
export function registerMultiSelect(action) {
	const actionObj = {
		name: 'files_scripts_multi_action',
		displayName: t('files_scripts', 'More actions'),
		iconClass: 'icon-files_scripts',
		order: 1001,
		action,
	}

	if (OCA.Files.App.fileList) {
		OCA.Files.App.fileList.registerMultiSelectFileAction(actionObj)
	} else {
		OC.Plugins.register('OCA.Files.FileList', {
			attach(fileList) {
				fileList.registerMultiSelectFileAction(actionObj)
			},
		})
	}
}

/**
 * Registers the action handler on the file context menu.
 *
 * @param {Function} actionHandler Callback to the handler
 */
export function registerFileSelect(actionHandler) {
	OCA.Files.fileActions.registerAction({
		name: 'files_scripts_action',
		displayName: t('files_scripts', 'More actions'),
		mime: 'all',
		permissions: OC.PERMISSION_READ,
		iconClass: 'icon-files_scripts',
		actionHandler,
	})
}

/**
 * Registers the action handler on the file context menu.
 *
 * @param {Script} script unique id to identify menu item
 * @param {Function} actionHandler Callback to the handler
 */
export function registerFileSelectDirect(script, actionHandler) {
	OCA.Files.fileActions.registerAction({
		name: 'files_scripts_action' + script.id,
		displayName: script.title,
		mime: 'all',
		permissions: OC.PERMISSION_READ,
		iconClass: 'icon-files_scripts',
		actionHandler,
	})
}
/**
 * Registers the multi-select action handler directly on the file context menu.
 *
 * @param {Script} script unique id to identify menu item
 * @param {Function} action Callback to the handler
 */
export function registerMultiSelectDirect(script, action) {
	const actionObj = {
		name: 'files_scripts_multi_action' + script.id,
		displayName: script.title,
		iconClass: 'icon-files_scripts',
		order: 1000,
		action,
	}

	if (OCA.Files.App.fileList) {
		OCA.Files.App.fileList.registerMultiSelectFileAction(actionObj)
	} else {
		OC.Plugins.register('OCA.Files.FileList', {
			attach(fileList) {
				fileList.registerMultiSelectFileAction(actionObj)
			},
		})
	}
}
