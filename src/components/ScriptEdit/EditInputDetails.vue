<template>
	<div>
		<div style="text-align: right;">
			<NcButton type="tertiary" @click="back" style="display: inline;">
				<template #icon>
					<KeyboardBackspace :size="16" />
				</template>
				{{ t('files_scripts', 'Back') }}
			</NcButton>
		</div>

		<div class="inputs">
			<NcTextField :value.sync="scriptInput.name"
				:label="t('files_scripts', 'Variable name')"
				:label-visible="true"
			/>
			<NcTextField :value.sync="scriptInput.description" :label="t('files_scripts', 'User prompt')"  :label-visible="true" />

			<div>
				<div class="input_label">{{ t('files_scripts', 'Input type') }}</div>
				<NcSelect
					class="full_width"
					@change="changeInputType"
					v-model="inputType"
					:options="Object.values(InputTypes)"
					trackBy="id"
					label="label"
					:clearable="false"
				/>
			</div>

			<MultiInput
				v-if="isInputMultiselect"
				:label="t('files_scripts', 'Multi-select options')"
				v-model="this.scriptInput.options.multiselectOptions"
			/>
			<MultiInput
				v-if="isInputFilepick"
				:label="t('files_scripts', 'Allowed MIME types (defaults to all)')"
				v-model="this.scriptInput.options.filepickMimes"
			/>
		</div>

		<NcButton type="primary" @click="save">
			<template #icon><ContentSave :size="20" /></template>
			{{ t('files_scripts', 'Save') }}
		</NcButton>
	</div>
</template>

<script lang="ts">
import { NcTextField, NcButton, NcSelect } from '@nextcloud/vue'
import ContentSave from 'vue-material-design-icons/ContentSave.vue'
import KeyboardBackspace from 'vue-material-design-icons/KeyboardBackspace.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import { ScriptInput } from '../../types/script'
import { translate as t } from '../../l10n'
import MultiInput from '../generic/MultiInput.vue'

const InputTypes = {
	text: { id: "text", label: t('files_scripts', 'Text')},
  checkbox: { id: "checkbox", label: t('files_scripts', 'Checkbox') },
	filepick: { id: "filepick", label: t('files_scripts', 'File picker') },
	multiselect: { id: "multiselect", label: t('files_scripts', 'Multi-select') },
}

export default {
	name: 'EditInputDetails',
	components: {
		NcTextField,
		NcButton,
		ContentSave,
		KeyboardBackspace,
		Plus,
		NcSelect,
		MultiInput
	},
	props: {
		scriptInput: Object as () => ScriptInput,
	},
	mounted() {
		this.scriptInput.value = null
	},
	data() {
		return {
			InputTypes,
			inputType: InputTypes[this.scriptInput.options.type] ?? InputTypes.text,
		}
	},
	computed: {
		isInputMultiselect() {
			return this.inputType?.id === 'multiselect'
		},
		isInputFilepick() {
			return this.inputType?.id === 'filepick'
		}
	},
	methods: {
		t,
		save() {
			this.$emit('save', this.scriptInput)
		},
		back() {
			this.$emit('cancel')
		},
		changeInputType(inputType) {
			this.scriptInput.options = {
				type: inputType.id,
				filepickMimes: [],
				multiselectOptions: []
			}
		}
	},
}
</script>

<style scoped>
.inputs {
	font-weight: bold;
	margin-bottom: 18px;
}
.input_label {
	padding: 4px 0;
}
.full_width {
	width: 100%;
}
.inputs ol {
	font-weight: normal;
	list-style-type: decimal;
	list-style-position: inside;
}
.inputs ol:before {
	font-weight:bold;
}
</style>
