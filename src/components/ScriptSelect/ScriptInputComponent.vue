<template>
	<div class="section-wrapper">

		<!-- Checkbox -->
		<template v-if="isInputCheckbox">
			<CheckboxMultipleMarkedCircle class="section-label" :size="20" />
			<NcCheckboxRadioSwitch
				class="section-details checkbox"
				type="switch"
				:checked.sync="scriptInput.value"
			>
				{{ this.scriptInput.description }}
			</NcCheckboxRadioSwitch>
		</template>

		<!-- Multiselect -->
		<template v-else-if="isInputMultiselect">
			<FormSelect class="section-label" :size="20" />
			<NcMultiselect v-model="localValue"
				class="section-details"
				:options="this.scriptInput.options.multiselectOptions"
				:placeholder="this.scriptInput.description"
				@change
			/>
		</template>

		<!-- Filepick -->
		<template v-else-if="isInputFilepick">
			<Folder class="section-label" :size="20" />
			<input v-model="localValue"
			 class="section-details"
			 type="text"
			 style="cursor: pointer;"
			 :placeholder="scriptInput.description"
			 @click="filepick"
			/>
		</template>


		<!-- Default: text -->
		<template v-else>
			<FormTextbox class="section-label" :size="20" />
			<input
				v-model="localValue"
				type="text"
				class="section-details"
				:placeholder="scriptInput.description"
			/>
		</template>
	</div>
</template>

<script lang="ts">
import { ScriptInput } from '../../types/script'
import FormTextbox from 'vue-material-design-icons/FormTextbox.vue';
import FormSelect from 'vue-material-design-icons/FormSelect.vue';
import CheckboxMultipleMarkedCircle from 'vue-material-design-icons/CheckboxMultipleMarkedCircle.vue';
import Folder from 'vue-material-design-icons/Folder.vue';
import { NcCheckboxRadioSwitch, NcMultiselect } from '@nextcloud/vue'
import { FilePickerBuilder, showError } from "@nextcloud/dialogs";
import { translate as t } from '../../l10n';
import * as path from 'path';

export default {
	name: 'ScriptInputComponent',
	props: {
		scriptInput: Object as () => ScriptInput,
		outputDirectory: String
	},
	components: {
		FormTextbox,
		FormSelect,
		CheckboxMultipleMarkedCircle,
		Folder,
		NcCheckboxRadioSwitch,
		NcMultiselect,
	},
	data() {
		return {
			localValue: this.scriptInput.value
		}
	},
	mounted() {
		this.resetValue()
	},
	computed: {
		type() {
			return this.scriptInput.options.type
		},
		isInputCheckbox() {
			return this.type === 'checkbox'
		},
		isInputMultiselect() {
			return this.type === 'multiselect'
		},
		isInputFilepick() {
			return this.type === 'filepick'
		},
	},
	methods: {
		t,
		resetValue() {
			this.localValue = this.getDefaultValue()
		},
		getDefaultValue() {
			switch (this.type) {
				case 'checkbox':
					return false
				case 'filepick':
					if (this.scriptInput.options.filepickMimes.includes('httpd/unix-directory')) {
						return this.outputDirectory
					}
				default: return ''
			}
		},
		async filepick() {
			const mimetypes = this.scriptInput.options.filepickMimes;
			const allowDirectories = mimetypes.includes('httpd/unix-directory')
			const pickerBuiler = (new FilePickerBuilder(this.scriptInput.description))
					.allowDirectories(allowDirectories)
					.startAt(this.localValue)

			if (mimetypes.length > 0) {
				pickerBuiler.setMimeTypeFilter(mimetypes)
			}

			const picker = pickerBuiler.build()
			try {
				const dir = await picker.pick() || '/'
				this.localValue = path.normalize(dir)
			} catch (error) {
				showError(error.message || t('files_scripts', 'Unknown error'))
			}
		},
	},
	watch: {
		localValue(newValue) {
			this.scriptInput.value = newValue
		}
	}
}
</script>

<style scoped lang="scss">
.checkbox {
	margin-left: 8px;
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
		z-index: 100000;
	}

	.section-details {
		flex-grow: 1;

		button.action-item--single {
			margin-top: -6px;
		}
	}
}
</style>
