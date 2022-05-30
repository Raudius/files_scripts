<template>
	<Modal v-if="showModal" @close="closeModal">
		<div class="file_scripts_modal">
			<h2>Title</h2>
			<div>A Nextcloud modal!</div>
		</div>
	</Modal>
</template>

<script>
import '@nextcloud/dialogs/styles/toast.scss'
import Modal from '@nextcloud/vue/dist/Components/Modal'

export default {
	name: 'ScriptSelectionModal',
	components: {
		Modal,
	},
	data() {
		return {
			showModal: false,
		}
	},
	computed: {
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
						console.debug(files)
						self.showModal = true
					},
				})
			},
			async compressFiles(fileIds, target) {
			},
		}

		OC.Plugins.register('OCA.Files.FileList', FilesPlugin)
	},

	methods: {
		closeModal() {
			this.showModal = false
		},
	},
}
</script>
<style scoped>
.file_scripts_modal {
	padding: 15px;
}
</style>
