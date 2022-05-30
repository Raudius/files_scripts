import { generateFilePath } from '@nextcloud/router'

import Vue from 'vue'
import VueCompositionApi, {createApp} from '@vue/composition-api'
import ScriptSelectionModal from "./components/ScriptSelectionModal.vue";
import Settings from "./Settings.vue";
import { createPinia, PiniaVuePlugin } from 'pinia'

declare var appName, __webpack_public_path__: string;
declare var OC,OCA,t,n: any

const ID_DIV_SETTINGS = 'files_scripts_settings'
const ID_DIV_FILES = 'files_scripts_files'

__webpack_public_path__ = generateFilePath(appName, '', 'js/')

Vue.mixin({ methods: { t, n } })
Vue.prototype.OC = OC
Vue.prototype.OCA = OCA

Vue.use(VueCompositionApi)
Vue.use(PiniaVuePlugin)
const pinia = createPinia()

/*
 * Render Vue app.
 * If we can find the settings mount DIV, we mount Settings.vue
 * Otherwise we add a modal mount DIV to the DOM, and mount ScriptSelectionModal.vue
 */
const settingsDiv = document.getElementById(ID_DIV_SETTINGS)
if (settingsDiv) {/*
	const app = new Vue({
		render: h => h(Settings),
		el: '#' + ID_DIV_SETTINGS,
		pinia,
	})*/
	const app = createApp(Settings)
	// @ts-ignore
	app.use(pinia)
	app.mount('#' + ID_DIV_SETTINGS)
} else {
	const div = document.createElement('div')
	div.id = ID_DIV_FILES
	document.body.appendChild(div)

	const app = new Vue({
		render: h => h(ScriptSelectionModal),
		pinia,
	})
	app.$mount('#' + ID_DIV_FILES)
}
