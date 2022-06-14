import { generateFilePath } from '@nextcloud/router'

import Vue from 'vue'
import ScriptSelectionModal from "./components/ScriptSelectionModal.vue";
import Settings from "./Settings.vue";
import Vuex from "vuex";
import { Tooltip } from '@nextcloud/vue'
import '@nextcloud/typings'

const ID_DIV_SETTINGS = 'files_scripts_settings'
const ID_DIV_FILES = 'files_scripts_files'

declare global {
	const OC, OCA: any
	const t: (ctxt: String, str: String, params?: Object) => String
	const n: (ctxt: String, str: String, params?: Object) => String
	var appName, __webpack_public_path__: string;
}

Vue.use(Vuex)
Vue.directive('tooltip', Tooltip)
__webpack_public_path__ = generateFilePath(appName, '', 'js/')

// Import store after vuex registration.
import { scripts as scriptsStore } from "./store/scripts";

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

	const app = new Vue({
		render: h => h(ScriptSelectionModal),
		el: '#' + ID_DIV_FILES,
		store: scriptsStore
	})
}
