<template>
	<NcModal v-if="showModal" @close="closeModal">
		<div class="file-scripts-modal">
			<h2 v-if=showScriptSelector>{{ t('files_scripts', 'Select action to perform') }}</h2>
			<h2 v-else>{{ selectedScript.title }}</h2>

			<div v-if="scripts === null" class="icon-loading" />
			<div v-else>
				<div v-if="showScriptSelector" class="section-wrapper">
					<FileCog class="section-label" :size="20" />
					<NcMultiselect v-model="selectedScript"
						class="section-details"
						:options="scripts"
						:placeholder="t('files_scripts', 'Select an action to perform')"
						track-by="id"
						:prevent-autofocus="true"
						label="title"
						@change="selectScript" />
				</div>

				<div v-if="selectedScript && selectedScript.requestDirectory" class="section-wrapper">
					<Folder class="section-label" :size="20" />
					<input v-model="outputDirectory"
						type="text"
						style="cursor: pointer;"
						class="section-details"
						:placeholder="t('files_scripts', 'Choose a folder …')"
						@click="pickOutputDirectory">
				</div>

				<ScriptInputComponent v-for="scriptInput in scriptInputs" :key="scriptInput.id" :scriptInput="scriptInput" />

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
import { loadState } from '@nextcloud/initial-state'
import '@nextcloud/dialogs/styles/toast.scss'
import { NcMultiselect, NcModal, NcButton } from "@nextcloud/vue";
import FileCog from 'vue-material-design-icons/FileCog.vue'
import ConsoleLine from 'vue-material-design-icons/ConsoleLine.vue'
import Play from 'vue-material-design-icons/Play.vue'
import Folder from 'vue-material-design-icons/Folder.vue'
import { showError, FilePickerBuilder, showSuccess } from '@nextcloud/dialogs'
import { api } from '../api/script'
import * as path from 'path'
import { translate as t } from '../l10n'
import {registerFileSelect, registerMultiSelect, registerFileSelectDirect, registerMultiSelectDirect} from '../files'
import { ScriptInput } from '../types/script'
import ScriptInputComponent from '../components/ScriptSelect/ScriptInputComponent.vue'

export default {
	name: 'ScriptSelect',
	components: {
		NcModal,
		NcButton,
		NcMultiselect,
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
			selectedFiles: [],
			outputDirectory: null,
			scriptInputs: [],
			loadingScriptInputs: false,
			showScriptSelector: true,
			actionsInMenu: loadState('files_scripts', 'actions_in_menu', false),
		}
	},

	computed: {
		scripts() {
			return this.$store.getters.getEnabledScripts
		},
		selectedDescription() {
			return this.selectedScript ? this.selectedScript.description : ''
		},
		readyToRun() {
			return this.selectedScript
				&& (!this.selectedScript.requestDirectory || this.outputDirectory)
				&& !this.loadingScriptInputs
				&& !this.isRunning
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
			this.showScriptSelector = true
			this.selectedScript = null
			this.selectedFiles = null
			this.scriptInputs = []
		},
		async selectScript(script) {
			this.outputDirectory = this.selectedFiles[0].path ?? '/'

			this.loadingScriptInputs = true
			this.scriptInputs = script ? await api.getScriptInputs(script.id) : []

			// Set default values
			this.scriptInputs = this.scriptInputs.map((scriptInput: ScriptInput) => {
				let value = ""

				if (scriptInput.options.type === 'multiselect') {
					value = scriptInput.options.multiselectOptions[0]
				}
				else if(scriptInput.options.type == "filepick") {
					value = this.outputDirectory
				}

				scriptInput.value = value
				return scriptInput
			})
			this.loadingScriptInputs = false
		},
		async run() {
			if (this.isRunning) {
				return
			}
			this.isRunning = true
			try {
				await api.runScript(this.selectedScript, this.outputDirectory, this.scriptInputs, this.selectedFiles)

				const currentDir = OCA.Files.App.getCurrentFileList().getCurrentDirectory()
				OCA.Files.App.fileList.changeDirectory(currentDir, true, true)

				showSuccess(t('files_scripts', 'Action completed!'))
				this.closeModal()
			} catch (response) {
				const errorObj = response?.response?.data
				const errorMsg = (errorObj && errorObj.error) ? errorObj.error : t('files_scripts', 'Action failed unexpectedly.')
				showError(errorMsg)
			}
			this.isRunning = false
		},

		async pickOutputDirectory() {
			const picker = (new FilePickerBuilder(t('files_scripts', 'Choose a folder …')))
				.allowDirectories(true)
				.setMimeTypeFilter(['httpd/unix-directory'])
				.startAt(this.outputDirectory)
				.build()

			try {
				const dir = await picker.pick() || '/'
				this.outputDirectory = path.normalize(dir)
			} catch (error) {
				showError(error.message || t('files_scripts', 'Unknown error'))
			}
		},
		attachMenuOption() {
			if (!this.scripts || this.scripts.length === 0) {
				return // No enabled scripts: no need to attach the options
			}
			const self = this
			if( self.actionsInMenu ) {
				this.scripts.forEach( function( script ) {
					const handler = function(files, context) {
						self.selectedScript = script
						self.showScriptSelector = false
						self.selectedFiles = (context && context.fileInfoModel) ? [context.fileInfoModel.attributes] : files

						self.selectScript(script)
						self.showModal = true
					}

					registerFileSelectDirect(script, handler)
					registerMultiSelectDirect(script, handler)
				});
			}
			else {
				registerMultiSelect(function(files) {
					self.showModal = true
					self.selectedFiles = files
				})
				registerFileSelect(function(file, context) {
					self.showModal = true
					self.selectedFiles = [context.fileInfoModel.attributes]
				})
			}
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
