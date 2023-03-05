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
 * @param {Int} menuId unique id to identify menu item
 * @param {String} menuTitle Text of menu item
 * @param {String} menuIcon  icon class (default 'icon-files_scripts')
 * @param {Function} actionHandler Callback to the handler
 */
export function registerFileSelectDirect(menuId, menuTitle, menuIcon, actionHandler ) {
	OCA.Files.fileActions.registerAction({
		name: 'files_scripts_action' + menuId,
		displayName: menuTitle,
		mime: 'all',
		permissions: OC.PERMISSION_READ,
		iconClass: menuIcon || 'icon-files_scripts',
		actionHandler,
	})
}
