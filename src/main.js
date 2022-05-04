import { generateFilePath } from '@nextcloud/router'

import Vue from 'vue'
import ActionsModal from './ActionsModal'
import Settings from './Settings'

const ID_DIV_SETTINGS = 'files_scripts_settings'
const ID_DIV_FILES = 'files_scripts_files'

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, '', 'js/')

Vue.mixin({ methods: { t, n } })

/*
 * Render Vue app.
 * If we can find the settings mount DIV, we mount Settings.vue
 * Otherwise we add a modal mount DIV to the DOM, and mount ActionsModal.vue
 */
const settingsDiv = document.getElementById(ID_DIV_SETTINGS)
if (settingsDiv) {
	const app = new Vue({
		render: h => h(Settings),
	})
	app.$mount('#' + ID_DIV_SETTINGS)
} else {
	const div = document.createElement('div')
	div.id = ID_DIV_FILES
	document.body.appendChild(div)

	const app = new Vue({
		render: h => h(ActionsModal),
	})
	app.$mount('#' + ID_DIV_FILES)
}
