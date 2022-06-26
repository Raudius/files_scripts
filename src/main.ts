import { generateFilePath } from '@nextcloud/router'
import ScriptSelect from './views/ScriptSelect.vue'
import Settings from './views/Settings.vue'
import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

// Import store after vuex registration.
const scriptsStore = require('./store/scripts').store

declare let appName: string

declare global {
	const OC, OCA: any // eslint-disable-line no-unused-vars
	const t: (...args) => string // eslint-disable-line no-unused-vars
	const n: (...args) => string // eslint-disable-line no-unused-vars
	let __webpack_public_path__: string // eslint-disable-line
}

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, '', 'js/')

const ID_DIV_SETTINGS = 'files_scripts_settings'
const ID_DIV_FILES = 'files_scripts_files'

/*
 * Render Vue app.
 * If we can find the settings mount DIV, we mount Settings.vue
 * Otherwise we add a modal mount DIV to the DOM, and mount ScriptSelectionModal.vue
 */
const settingsDiv = document.getElementById(ID_DIV_SETTINGS)
if (settingsDiv) {
	new Vue({ // eslint-disable-line no-new
		el: '#' + ID_DIV_SETTINGS,
		render: h => h(Settings),
		store: scriptsStore,
	})
} else {
	const div = document.createElement('div')
	div.id = ID_DIV_FILES
	document.body.appendChild(div)

	new Vue({ // eslint-disable-line no-new
		el: '#' + ID_DIV_FILES,
		render: h => h(ScriptSelect),
		store: scriptsStore,
	})
}
