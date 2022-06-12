import { generateFilePath } from '@nextcloud/router'

import Vue from 'vue'
import ScriptSelectionModal from "./components/ScriptSelectionModal.vue";
import Settings from "./Settings.vue";
import Vuex from "vuex";
import '@nextcloud/typings'


Vue.mixin({ methods: { t, n } })
Vue.use(Vuex)
Vue.prototype.OC = OC
__webpack_public_path__ = generateFilePath(appName, '', 'js/')

const ID_DIV_SETTINGS = 'files_scripts_settings'
const ID_DIV_FILES = 'files_scripts_files'

declare global {
	const OC: Nextcloud.v24.OC
	const t: (ctxt: String, str: String, params?: Object) => String
	const n: (ctxt: String, str: String, params?: Object) => String
	var appName, __webpack_public_path__: string;
}

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
	})
	app.$mount('#' + ID_DIV_FILES)
}
