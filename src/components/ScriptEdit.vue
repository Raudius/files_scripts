<template>
	<Modal v-if="showModal" size="full" @close="closeModal" :spread-navigation="true">
		<Actions>
			<ActionButton @click="saveScript">
				<template #icon><Save :size="20" /></template>
			</ActionButton>
		</Actions>
		<div v-if="saving" style="display: inline-block;" class="icon-loading"></div>


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

				<CheckboxRadioSwitch type="switch" :checked="!!script.requestDirectory" @update:checked="toggleRequestDirectory">
					Request output location
				</CheckboxRadioSwitch>

				<CheckboxRadioSwitch type="switch" :checked="!!script.background" @update:checked="toggleBackground">
					Run in background
				</CheckboxRadioSwitch>

				<EditInputs v-bind:script-id="this.script.id" v-on:changed="updateInputs" />
			</div>


			<div class="script-editor">
				<CodeMirror style="height: 100%" v-model="scriptProgram" :options="cmOption" />
			</div>
		</div>
	</Modal>
</template>

<script lang="ts">
import CheckboxRadioSwitch from "@nextcloud/vue/dist/Components/CheckboxRadioSwitch";
import Save from "vue-material-design-icons/ContentSave.vue";
import Modal from '@nextcloud/vue/dist/Components/Modal'
import Actions from '@nextcloud/vue/dist/Components/Actions'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import EditInputs from './ScriptEdit/EditInputs.vue'

import 'codemirror/mode/lua/lua.js'
import 'codemirror/addon/edit/matchbrackets.js'
import 'codemirror/addon/hint/show-hint.js'
import {mapState} from "vuex";
import {showError, showSuccess} from "@nextcloud/dialogs";
import {ScriptInput} from "../types/script";
import {api} from "../api/script";
const CodeMirror = require('vue-codemirror').codemirror;

export default {
	name: 'ScriptEdit',
	components: {
		Save,
		Modal,
		Actions,
		ActionButton,
		CodeMirror,
		CheckboxRadioSwitch,
		EditInputs
	},
	data() {
		return {
			scriptInputs: [],
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
			const self = this;
			this.saving = true
			this.$store.dispatch('saveScript')
				.then(function() {
					api.updateScriptInputs(self.script, self.scriptInputs)
				})
				.then(() => {
					showSuccess('Saved', { timeout: 2000 })
				})
				.catch((error) => {
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
			this.$store.commit('selectedToggleValue', 'enabled')
		},
		toggleBackground() {
			this.$store.commit('selectedToggleValue', 'background')
		},
		toggleRequestDirectory() {
			this.$store.commit('selectedToggleValue', 'requestDirectory')
		},
		updateInputs(scriptInputs: ScriptInput[]) {
			console.log('updateInputs')
			this.scriptInputs = scriptInputs;
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
	height: 90vh;
	margin-right: 12px;
	overflow-y: auto;
	overflow-x: clip;
}

.script-editor {
	flex: 1 0;
	border: solid 1px slategray;
	height: 90vh;
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