<template>
	<Modal v-if="showModal" @close="closeModal">
		<div class="file-scripts-modal">
			<h2>Select action to perform</h2>
			<div v-if="isLoading" class="icon-loading"></div>
			<div v-else>
				<div class="section-wrapper">
					<div class="section-label">
						<FileCog title="" :size="20" />
					</div>
					<div class="section-details">
						<Multiselect
							class="multiselect"
							v-model="selectedScript"
							:options="scripts"
							placeholder="Select an action to perform"
							track-by="id"
							label="title"
						/>
					</div>
				</div>

				<div class="script-info">
					{{ this.selectedDescription }}
				</div>

				<Button class="btn-run" type="primary" @click="run">
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
import Play from 'vue-material-design-icons/Play.vue'
import {showError} from "@nextcloud/dialogs";

export default {
	name: 'ScriptSelect',
	components: {
		Modal,
		Button,
		Multiselect,
		FileCog,
		Play
	},
	data() {
		return {
			showModal: false,
			isRunning: false,
			selectedScript: null,
			selectedFiles: [],
			readableName: 'test'
		}
	},

	computed: {
		scripts: function() {
			return this.$store.getters.getScripts
		},
		selectedDescription: function () {
			return this.selectedScript ? this.selectedScript.description : ''
		},
		isLoading: function (): boolean {
			return this.isRunning || this.scripts === null;
		}
	},

	async mounted() {
		const self = this
		const FilesPlugin = {
			attach(fileList) {
				fileList.registerMultiSelectFileAction({
					name: 'files_actions',
					displayName: t('files_actions', 'Run action'),
					iconClass: 'icon-category-workflow',
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
		showModal(newVal, oldVal) {
			if (newVal === true && !this.scripts) {
				this.$store.dispatch('fetchScripts')
			}
		}
	},

	methods: {
		closeModal() {
			this.showModal = false
			this.isRunning = false
			this.selectedScript = null
			this.selectedFiles = null
		},
		run() {
			if (this.isRunning) {
				return;
			}
			this.isRunning = true;

			const payload = {
				script: this.selectedScript,
				files: this.selectedFiles
			}
			const self = this;
			this.$store.dispatch('runScript', payload)
				.then(() => {
					self.closeModal()
				})
				.catch((response) => {
					const errorObj = response.response.data
					const errorMsg = (errorObj && errorObj.error) ? errorObj.error : "Action failed without error."

					showError(errorMsg)
					this.closeModal()
				})
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

.multiselect {
	width: 100%;
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
