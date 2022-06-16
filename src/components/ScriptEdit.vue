<template>
	<Modal v-if="showModal" size="full" @close="closeModal" :spread-navigation="true">
		<Actions>
			<ActionButton @click="saveScript">
				<template #icon><Save :size="20" /></template>
			</ActionButton>
		</Actions>
		<!-- TODO: Replace with LoadingIcon-->
		<span style="position: fixed;"><Save :size="20" v-if="saving" /></span>

		<div class="container-script-edit">
			<div class="script-details">
				<input type="text"
				   class="input-script-name"
				   placeholder="Script name"
				   v-model="scriptTitle"
				>
				<textarea
					class="input-script-description"
					placeholder="A short description of what this action will do..."
					v-model="scriptDescription"
					rows="6"
				></textarea>

				<CheckboxRadioSwitch type="switch" :checked="!!script.enabled" @update:checked="toggleEnabled">
					Enable script
				</CheckboxRadioSwitch>
			</div>

			<div class="script-editor">
				<CodeMirror style="height: 100%" v-model="scriptProgram" :options="cmOption" />
			</div>
		</div>
	</Modal>
</template>

<script lang="ts">
import Button from "@nextcloud/vue/dist/Components/Button";
import CheckboxRadioSwitch from "@nextcloud/vue/dist/Components/CheckboxRadioSwitch";
import Save from "vue-material-design-icons/ContentSave.vue";
import Modal from '@nextcloud/vue/dist/Components/Modal'
import Actions from '@nextcloud/vue/dist/Components/Actions'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'

import 'codemirror/mode/lua/lua.js'
import 'codemirror/addon/edit/matchbrackets.js'
import 'codemirror/addon/hint/show-hint.js'
import {mapState} from "vuex";
import {showError} from "@nextcloud/dialogs";
const CodeMirror = require('vue-codemirror').codemirror;

export default {
	name: 'ScriptEdit',
	components: {
		Button,
		Save,
		Modal,
		Actions,
		ActionButton,
		CodeMirror,
		CheckboxRadioSwitch
	},
	data() {
		return {
			saving: false,
			cmOption: {
				tabSize: 4,
				styleActiveLine: true,
				lineNumbers: true,
				line: true,
				foldGutter: true,
				styleSelectedText: true,
				matchBrackets: true,
				showCursorWhenSelecting: true,
				mode: 'text/x-lua',
				theme: 'idea',
				extraKeys: { Ctrl: 'autocomplete' },
				hintOptions: {
					completeSingle: true,
				},
			},
		}
	},
	computed: {
		...mapState({script: 'selectedScript'}),
		showModal: function (): boolean {
			return !!this.script
		},
		scriptTitle: {
			get() {
				return this.script.title
			},
			set (value) {
				this.$store.commit('updateCurrentScript', {title: value})
			}
		},
		scriptProgram: {
			get() {
				return this.script.program
			},
			set (value) {
				this.$store.commit('updateCurrentScript', {program: value})
			}
		},
		scriptDescription: {
			get() {
				return this.script.description
			},
			set (value) {
				this.$store.commit('updateCurrentScript', {description: value})
			}
		},
	},

	methods: {
		saveScript() {
			const script = this.script;
			this.saving = true
			this.$store.dispatch('saveScript')
				.then(() => {
					if (!script.id) {
						this.$store.dispatch('fetchScripts')
						this.closeModal();
					}
				})
				.catch((error) => {
					console.log(error);
					let message = 'An error occurred during saving'
					if (error.response && error.response.data.error) {
						message = error.response.data.error
					}
					showError(message)
				})
				.then(() => {
					this.saving = false;
				})
		},
		toggleEnabled() {
			this.$store.commit('selectedToggleEnabled')
		},
		closeModal() {
			this.$store.commit('clearSelected')
		},
	}
}
</script>

<style scoped>
@import 'codemirror/lib/codemirror.css';
@import 'codemirror/lib/codemirror.css';
@import 'codemirror/theme/idea.css';
@import 'codemirror/theme/base16-light.css';
@import '../../css/codemirror.css';

.container-script-edit {
	display: flex;
	margin: 10px;
}
.script-details {
	flex: 0 0 25%;
	height: 80vh;
	margin-right: 12px;
}

.script-editor {
	flex: 1 0;
	border: solid 1px slategray;
	height: 80vh;
}

.input-script-name {
	width: 100%;
	font-size: large;
}
.input-script-description {
	width: 100%;
	resize: vertical;
}
</style>
