<template>
	<Modal v-if="showModal" @close="closeModal">
		<div class="file-scripts-modal">
			<h2>Select action to perform</h2>
			<div v-if="isLoading" class="icon-loading"></div>
			<div v-else>
				<div class="section-wrapper">
					<FileCog class="section-label" :size="20" />
					<Multiselect
						class="section-details"
						v-model="selectedScript"
						:options="scripts"
						placeholder="Select an action to perform"
						track-by="id"
						label="title"
						@change="selectScript"
					/>
				</div>

				<div class="section-wrapper" v-if="selectedScript && selectedScript.requestDirectory">
					<Folder class="section-label" :size="20" />
					<input type="text" style="cursor: pointer;" class="section-details" v-model="outputDirectory" @click="pickOutputDirectory" placeholder="Choose a folder..." />
				</div>

				<div v-if="loadingScriptInputs" class="input-loader icon-loading"></div>
				<div class="section-wrapper" v-for="scriptInput in scriptInputs">
					<ConsoleLine class="section-label" :size="20" />
					<input type="text" class="section-details" v-model="scriptInput.value" :placeholder="scriptInput.description" />
				</div>

				<div class="script-info">
					{{ this.selectedDescription }}
				</div>

				<Button class="btn-run" type="primary" :disabled="!readyToRun" @click="run">
					<template #icon> <Play :size="20" /> </template>
					Execute
				</Button>
			</div>
		</div>
	</Modal>
</template>

<script lang="ts">
import '@nextcloud/dialogs/styles/toast.scss'
import Multiselect from '@nextcloud/vue/dist/Components/Multiselect'
import Modal from '@nextcloud/vue/dist/Components/Modal'
import Button from '@nextcloud/vue/dist/Components/Button'
import FileCog from 'vue-material-design-icons/FileCog.vue'
import ConsoleLine from 'vue-material-design-icons/ConsoleLine.vue'
import Play from 'vue-material-design-icons/Play.vue'
import Folder from 'vue-material-design-icons/Folder.vue'
import {showError, FilePickerBuilder} from "@nextcloud/dialogs";
import {api} from "../api/script";
import * as path from "path";

export default {
	name: 'ScriptSelect',
	components: {
		Modal,
		Button,
		Multiselect,
		FileCog,
		ConsoleLine,
		Folder,
		Play
	},
	data() {
		return {
			showModal: false,
			isRunning: false,
			selectedScript: null,
			selectedFiles: [],
			outputDirectory: null,
			readableName: 'test',
			scriptInputs: [],
			loadingScriptInputs: false
		}
	},

	computed: {
		scripts() {
			return this.$store.getters.getEnabledScripts
		},
		selectedDescription() {
			return this.selectedScript ? this.selectedScript.description : ''
		},
		isLoading(): boolean {
			return this.isRunning || this.scripts === null;
		},
		readyToRun() {
			return this.selectedScript
				&& (!this.selectedScript.requestDirectory || this.outputDirectory)
				&& !this.loadingScriptInputs;
		}
	},

	async mounted() {
		const self = this
		const FilesPlugin = {
			attach(fileList) {

				fileList.registerMultiSelectFileAction({
					name: 'files_actions',
					displayName: 'Run action',
					iconClass: 'icon-files_scripts',
					order: 1001,
					action: (files) => {
						self.showModal = true
						self.selectedFiles = files
					},
				})
			}
		}

		await OC.Plugins.register('OCA.Files.FileList', FilesPlugin)
	},
	watch: {
		showModal(newVal) {
			(newVal === true && !this.scripts) && this.$store.dispatch('fetchScripts')
		}
	},

	methods: {
		closeModal() {
			this.showModal = false
			this.isRunning = false
			this.selectedScript = null
			this.selectedFiles = null
			this.scriptInputs = []
		},
		async selectScript(script) {
			this.outputDirectory = this.selectedFiles[0].path ?? '/'

			this.loadingScriptInputs = true;
			this.scriptInputs = script ? await api.getScriptInputs(script.id) : []
			this.loadingScriptInputs = false;
		},
		async run() {
			if (this.isRunning) {
				return;
			}
			this.isRunning = true;
			try {
				await api.runScript(this.selectedScript, this.outputDirectory, this.scriptInputs, this.selectedFiles);

				const currentDir = OCA.Files.App.getCurrentFileList().getCurrentDirectory()
				OCA.Files.App.fileList.changeDirectory(currentDir, true, true);
				this.closeModal()
			} catch (response) {
				const errorObj = response?.response?.data
				const errorMsg = (errorObj && errorObj.error) ? errorObj.error : "Action failed unexpectedly."

				showError(errorMsg)
			}
			this.isRunning = false;
		},

		async pickOutputDirectory() {
			const picker = (new FilePickerBuilder('Choose a folder...'))
				.allowDirectories(true)
				.setMimeTypeFilter(['httpd/unix-directory'])
				.startAt(this.outputDirectory)
				.build()

			try {
				const dir = await picker.pick() || '/'
				this.outputDirectory = path.normalize(dir);
			} catch (error) {
				showError(error.message || 'Unknown error')
			}
		}
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

.btn-run {
	float: right;
	margin-bottom: 15px;
}

.input-loader {
	margin-top: 18px;
}

.section-wrapper {
	display: flex;
	max-width: 100%;
	margin-top: 10px;

	.section-label {
		background-position: 0px center;
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

<style>
/* Hack to get multiselect to show correctly (over the modal mask) */
.file-scripts-modal .multiselect__content-wrapper {
	position:fixed !important;
	width: auto !important;
	min-width: 400px !important;
}
</style>
