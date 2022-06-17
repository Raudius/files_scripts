import { generateFilePath } from '@nextcloud/router'

import Vue from 'vue'
import ScriptSelect from "./views/ScriptSelect.vue";
import Settings from "./views/Settings.vue";
import Vuex from "vuex";

Vue.use(Vuex)

// Import store after vuex registration.
const scriptsStore = require('./store/scripts').store


declare global {
	const OC, OCA: any
	const t: (ctxt: String, str: String, params?: Object) => String
	const n: (ctxt: String, str: String, params?: Object) => String
	const appName: string
	let __webpack_public_path__: string
}

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
	new Vue({
		render: h => h(Settings),
		el: '#' + ID_DIV_SETTINGS,
		store: scriptsStore
	})
} else {
	const div = document.createElement('div')
	div.id = ID_DIV_FILES
	document.body.appendChild(div)

	new Vue({
		render: h => h(ScriptSelect),
		el: '#' + ID_DIV_FILES,
		store: scriptsStore
	})
}
