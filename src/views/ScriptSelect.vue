<template>
	<NcModal v-if="showModal" @close="closeModal">
		<div class="file-scripts-modal">
			<h2>
				<template v-if="showScriptSelection">
					{{ t('files_scripts', 'Select action to perform') }}
				</template>
				<template v-else>{{ selectedScript.title }}</template>
			</h2>

			<div v-if="scripts === null" class="icon-loading" />
			<div v-else>
				<div v-if="showScriptSelection" class="section-wrapper">
					<FileCog class="section-label" :size="20" />
					<NcSelect v-model="selectedScript"
						class="section-details"
						:tabindex="-1"
						:options="scripts"
						:placeholder="t('files_scripts', 'Select an action to perform')"
						track-by="id"
						:prevent-autofocus="true"
						label="title"
						@input="selectScript" />
				</div>

				<ScriptInputComponent v-for="scriptInput in scriptInputs" :key="scriptInput.id" :scriptInput="scriptInput" :outputDirectory="outputDirectory" />

				<div class="script-info">
					{{ selectedDescription }}
				</div>

				<div style="text-align: right;">
					<div v-if="loadingScriptInputs || isRunning" class="input-loader icon-loading display-inline" />
					<NcButton class="display-inline"
						type="primary"
						:disabled="!readyToRun"
						@click="run">
						<template #icon>
							<Play :size="20" />
						</template>
						{{ t('files_scripts', 'Execute') }}
					</NcButton>
				</div>
			</div>
		</div>
	</NcModal>
</template>

<script lang="ts">
import {loadState} from '@nextcloud/initial-state'
import {NcButton, NcModal, NcSelect} from "@nextcloud/vue";
import FileCog from 'vue-material-design-icons/FileCog.vue'
import ConsoleLine from 'vue-material-design-icons/ConsoleLine.vue'
import Play from 'vue-material-design-icons/Play.vue'
import Folder from 'vue-material-design-icons/Folder.vue'
import {api} from '../api/script'
import {translate as t} from '../l10n'
import {registerMenuOption, reloadCurrentDirectory} from '../files'
import ScriptInputComponent from '../components/ScriptSelect/ScriptInputComponent.vue'
import {MessageType, showMessage} from "../types/Messages";
import {Node} from "@nextcloud/files";
import {scriptAllowedForNodes} from "../types/script";

export default {
	name: 'ScriptSelect',
	components: {
		NcModal,
		NcButton,
		NcSelect,
		FileCog,
		ConsoleLine,
		Folder,
		Play,
		ScriptInputComponent
	},
	data() {
		return {
			showModal: false,
			isRunning: false,
			selectedScript: null,
			selectedFiles: [] as Node[],
			outputDirectory: null,
			scriptInputs: [],
			loadingScriptInputs: false,
			showScriptSelection: true
		}
	},

	computed: {
		filterMimetype() {
			const mimetypes = [ ...new Set(this.selectedFiles.map(f => f.mime)) ]
			return mimetypes.length === 1 ? mimetypes[0] : null
		},
		scripts() {
			return this.allScripts.filter(s => scriptAllowedForNodes(s, this.selectedFiles))
		},
		allScripts() {
			return this.$store.getters.getEnabledScripts
		},
		selectedDescription() {
			return this.selectedScript ? this.selectedScript.description : ''
		},
		readyToRun() {
			return this.selectedScript && !this.loadingScriptInputs && !this.isRunning
		},
	},
	watch: {
		showModal(newVal) {
			(newVal === true && !this.scripts) && this.$store.dispatch('fetchScripts')
		},
	},

	mounted() {
		this.$store.dispatch('fetchScripts')
			.then(this.attachMenuOption)
	},

	methods: {
		t,
		closeModal() {
			this.showModal = false
			this.isRunning = false
			this.selectedScript = null
			this.selectedFiles = null
			this.scriptInputs = []
		},
		async selectScript(script) {
			this.selectedScript = script
			this.outputDirectory = this.selectedFiles[0].dirname

			this.loadingScriptInputs = true
			this.scriptInputs = script ? await api.getScriptInputs(script.id) : []
			this.loadingScriptInputs = false
		},
		async run() {
			if (this.isRunning) {
				return
			}

			this.isRunning = true
			let messages = []
			let view_files = []

			try {
				let response = await api.runScript(this.selectedScript, this.scriptInputs, this.selectedFiles)
				reloadCurrentDirectory(null)

				view_files = response.view_files ?? [];
				messages = response.messages ?? []
				messages.push({ message: (t('files_scripts', 'Action completed!')), type: MessageType.SUCCESS })
				this.closeModal()
			} catch (response) {
				const errorObj = response?.response?.data
				const errorMsg = (errorObj && errorObj.error) ? errorObj.error : t('files_scripts', 'Action failed unexpectedly.')

				messages = errorObj?.messages ?? []
				messages.push({ message: errorMsg, type: MessageType.ERROR })
			}
			this.isRunning = false

			for (let message of messages) {
				showMessage(message);
			}

			if (view_files.length > 0) {
				OCA.Viewer.open({
					fileInfo: view_files[0],
					list: view_files,
				})
			}
		},

		/**
		 * Select the files and optionally the script (if selecting directly from the file menu)
		 */
		selectFiles(files) {
			this.selectedFiles = files
			this.showScriptSelection = true

			this.showModal = true
		},

		selectFilesWithScript(script) {
			return (files: Node[]) => {
				this.selectFiles(files)
				this.selectScript(script)
				this.showScriptSelection = false
			}
		},

		attachMenuOption() {
			if (!this.allScripts || this.allScripts.length === 0) {
				console.debug("[files_scripts] No enabled scripts for this user, not attaching menu option.")
				return // No enabled scripts: no need to attach the options
			}

			// Attach context menu options if enabled
			const actionsInMenu = loadState('files_scripts', 'actions_in_menu', false);
			if (actionsInMenu) {
				this.allScripts.forEach((script) => {
					const selectFileFunc = this.selectFilesWithScript(script)
					registerMenuOption(selectFileFunc, script)
				});
			}

			// Attach "More actions..." menu options
			registerMenuOption(this.selectFiles)
		},
	},
}
</script>
<style scoped lang="scss">
.file-scripts-modal {
	height: auto;
	padding: 15px;
}

.script-info {
	margin-top: 10px;
	min-height: 5vh;
}

.display-inline {
	display: inline !important;
	margin-left: 24px;
}

.input-loader {
	margin-top: 18px;
}

.section-wrapper {
	display: flex;
	max-width: 100%;
	margin-top: 10px;

	.section-label {
		background-position: 0 center;
		width: 28px;
		flex-shrink: 0;
		text-align: center;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.section-details {
		flex-grow: 1;

		button.action-item--single {
			margin-top: -6px;
		}
	}
}
</style>

<style lang="scss">
/* Hack to get multiselect to show correctly (over the modal mask) */
.file-scripts-modal .multiselect__content-wrapper {
	position:fixed !important;
	width: auto !important;
	min-width: 400px !important;
}
</style>
