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
				   :placeholder="t('Script name')"
				   v-model="scriptTitle"
				>
				<textarea
					class="input-script-description"
					:placeholder="t('A short description of what this action will do...')"
					v-model="scriptDescription"
					rows="6"
				></textarea>

				<CheckboxRadioSwitch type="switch" :checked="!!script.enabled" @update:checked="toggleEnabled">
					{{ t('Enable script') }}
				</CheckboxRadioSwitch>

				<CheckboxRadioSwitch type="switch" :checked="!!script.requestDirectory" @update:checked="toggleRequestDirectory">
					{{ t('Request output location') }}
				</CheckboxRadioSwitch>
<!-- TODO: Uncomment when background jobs gets implemented
				<CheckboxRadioSwitch type="switch" :checked="!!script.background" @update:checked="toggleBackground">
					Run in background
				</CheckboxRadioSwitch>
-->
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
import {translate as t} from "../l10n";
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
		const darkMode = document.body.classList.contains('theme--dark');

		return {
			scriptInputs: [],
			dirtyInputs: false,
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
				theme: darkMode ? 'material-darker' : 'idea',
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
		t,
		saveScript() {
			const self = this;
			this.saving = true
			this.saveScriptAsync()
				.then(() => {
					self.dirtyInputs = false;
					showSuccess(t('Saved'), { timeout: 2000 })
				})
				.catch((error) => {
					let message = t('An error occurred during saving')
					if (error.response && error.response.data.error) {
						message = error.response.data.error
					}
					showError(message)
				})
				.then(() => {
					this.saving = false;
				})
		},
		async saveScriptAsync() {
			await this.$store.dispatch('saveScript')
			if (this.dirtyInputs) {
				await api.updateScriptInputs(this.script, this.scriptInputs)
			}
		},
		updateInputs(scriptInputs: ScriptInput[]) {
			this.scriptInputs = scriptInputs;
			this.dirtyInputs = true;
		},
		closeModal() {
			this.$store.commit('clearSelected')
			this.dirtyInputs = false;
			this.scriptInputs = [];
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
	}
}
</script>

<style scoped>
@import 'codemirror/lib/codemirror.css';
@import 'codemirror/theme/idea.css';
@import 'codemirror/theme/material-darker.css';
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
