<template>
	<div>
		<h3>{{ t('files_scripts', 'User inputs') }}</h3>
		<div class="section-description">
			{{ t('files_scripts', 'Specify any number of input values the user may provide when running this action. These will be accessible to the script via the get_input() function.') }}
		</div>

		<div v-if="loading" class="icon-loading" />
		<div v-else-if="this.editingInput">
			<EditInputDetails :scriptInput="this.editingInput" @save="saveInput" @cancel="resetEditing"/>
		</div>
		<div v-else>
			<div v-for="(scriptInput, idx) in scriptInputs"
					 :key="idx"
					 class="script-input">
				<div class="input-name">{{ scriptInput.name }}</div>
				<div class="input-description">{{ scriptInput.description }}</div>
				<div class="input-action">
					<NcActions>
						<NcActionButton icon="icon-rename" :close-after-click="true" @click="edit(scriptInput)">
							{{ t('files_scripts', 'Edit') }}
						</NcActionButton>
						<NcActionButton @click="remove(scriptInput)">
							{{ t('files_scripts', 'Delete') }}
							<template #icon>
								<Delete :size="20" @click="remove(scriptInput)" />
							</template>
						</NcActionButton>
					</NcActions>
				</div>
			</div>
			<div>
				<NcButton type="secondary" @click="addInput">
					<template #icon><Plus :size="16" /></template>
					{{ t('files_scripts', 'Add input') }}
				</NcButton>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
import { NcActions, NcActionButton, NcButton } from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import {createScriptInput, ScriptInput} from '../../types/script'
import Vue from 'vue'
import { api } from '../../api/script'
import { translate as t } from '../../l10n'
import EditInputDetails from './EditInputDetails.vue'
import {showInfo} from "@nextcloud/dialogs";

export default {
	name: 'EditInputs',
	components: {
		EditInputDetails,
		NcActions,
		NcActionButton,
		NcButton,
		Plus,
		Delete,
	},
	props: {
		scriptId: Number,
	},
	data() {
		return {
			inputName: '',
			inputDescription: '',
			scriptInputs: {},
			loading: true,
			editingInputName: null,
			editingInput: null
		}
	},
	mounted() {
		this.fetchInputs()
	},
	methods: {
		t,
		addInput() {
			this.edit(createScriptInput('', ''))
		},
		saveInput(scriptInput: ScriptInput) {
			if (scriptInput.name === '') {
				showInfo(t('files_scripts', 'Script input name cannot be empty'))
				return;
			}

			if (scriptInput.name !== this.editingInputName && (scriptInput.name in this.scriptInputs)) {
				showInfo(t('files_scripts', 'Script input name already in use.'))
				return;
			}

			if (scriptInput.name !== this.editingInputName) {
				Vue.delete(this.scriptInputs, this.editingInputName)
			}
			this.scriptInputs[scriptInput.name] = scriptInput
			this.resetEditing();
			this.updated()
		},
		resetEditing() {
			this.editingInput = null
			this.editingInputName = null
		},
		edit(scriptInput: ScriptInput) {
			this.editingInput = Object.assign({}, scriptInput)
			this.editingInputName = scriptInput.name
		},
		remove(scriptInput: ScriptInput) {
			Vue.delete(this.scriptInputs, scriptInput.name)
			this.updated()
		},
		updated() {
			this.$emit('changed', this.scriptInputs)
		},
		async fetchInputs() {
			if (this.scriptId === null) {
				this.loading = false
				return
			}

			const inputs = await api.getScriptInputs(this.scriptId)
			const scriptInputs = {}
			inputs.forEach(function(input: ScriptInput) {
				scriptInputs[input.name] = input
			})
			this.scriptInputs = scriptInputs
			this.loading = false
			this.updated()
		},
	},
}
</script>

<style scoped>
h3 {
	margin-top: 24px;
}

.section-description {
	opacity: .7;
	margin-bottom: 16px;
}

.script-input {
	display: flex;
}

.input-name {
	flex: 0 1 30%;
}

.input-description {
	flex: 1 2 60%;
}

.input-action {
	flex: 0 0 10%;
}
</style>
