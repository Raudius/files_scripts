<template>
	<div>
		<ScriptEdit />

		<!-- File actions section -->
		<NcSettingsSection :title="t('files_scripts', 'File actions')"
			:description="t('files_scripts', 'File actions are small Lua scripts that can create, modify, and/or delete files programatically. These actions may be triggered by users to be run on their files. Please read the documentation for more information.')"
			doc-url="https://github.com/Raudius/files_scripts/blob/master/docs/Admin.md">

			<NcNoteCard type="warning" v-if="!this.pluginAvailable && !this.usePhpInterpreter">
				<p>{{ t('files_scripts', 'File actions are not available because there is no available interpreter. Either install the Lua plugin on the server or enable the experimental interpreter.') }}</p>
			</NcNoteCard>

			<NcButton type="primary" @click="newScript">
				<template #icon>
					<Plus :size="20" />
				</template>
				{{ t('files_scripts', 'New action') }}
			</NcButton>

			<ul class="script-cards">
				<ScriptCard v-for="script in scripts"
					:id="script.id"
					:key="script.id"
					:script="script"
					:title="script.title"
					@delete="deleteScript"
					@select="selectScript" />
			</ul>

			<div v-if="isLoading" class="icon-loading" />
			<NcEmptyContent v-if="scripts && scripts.length === 0" class="empty-content">
				{{ t('No actions') }}
				<template #icon>
					<FileCog />
				</template>
				<template #desc>
					{{ t('No file actions exist.') }}
				</template>
			</NcEmptyContent>
		</NcSettingsSection>

		<!-- PHP interpreter section -->
		<NcSettingsSection
			:title="t('files_scripts', 'Experimental interpreter')"
			:description="t('files_scripts', 'It is highly recommended to run the PHP Lua extension on your server, if this is not possible, the experimental Lua interpreter may be used. This interpreter is still under development and may not always produce the expected results.')"
		>
			<NcCheckboxRadioSwitch type="switch" :checked="this.usePhpInterpreter" @update:checked="toggleExperimentalInterpreter">
				{{ t('files_scripts', 'Use experimental interpreter') }}
			</NcCheckboxRadioSwitch>
		</NcSettingsSection>
	</div>
</template>

<script lang="ts">
import { loadState } from '@nextcloud/initial-state'
import { showError } from '@nextcloud/dialogs'
import Plus from 'vue-material-design-icons/Plus.vue'
import FileCog from 'vue-material-design-icons/FileCog.vue'
import { NcButton, NcEmptyContent, NcSettingsSection, NcCheckboxRadioSwitch } from '@nextcloud/vue'
import ScriptEdit from '../components/ScriptEdit.vue'
import ScriptCard from '../components/ScriptCard.vue'
import { mapState } from 'vuex'
import { Script } from '../types/script'
import { translate as t } from '../l10n'
import axios from "@nextcloud/axios";
import {generateUrl} from "@nextcloud/router";

export default {
	name: 'Settings',
	components: {
		NcButton,
		NcEmptyContent,
		NcSettingsSection,
		NcCheckboxRadioSwitch,
		ScriptEdit,
		ScriptCard,
		Plus,
		FileCog,
	},

	computed: {
		...mapState({
			selectedScript: 'selectedScript',
			scripts: 'scripts',
		}),
		isLoading() {
			return this.scripts === null
		},
	},

	data() {
		return {
			usePhpInterpreter: loadState('files_scripts', 'use_php_interpreter', false),
			pluginAvailable: loadState('files_scripts', 'lua_plugin_available', false)
		}
	},

	mounted() {
		this.$store.dispatch('fetchScripts')
	},

	methods: {
		t,
		newScript() {
			this.$store.commit('newScript')
		},
		selectScript(script: Script) {
			this.$store.commit('setSelectedScript', script)
		},
		deleteScript(script: Script) {
			this.$store.dispatch('deleteScript', script)
		},
		async toggleExperimentalInterpreter(value) {
			this.usePhpInterpreter = value
			try {
				await axios.post(generateUrl('/apps/files_scripts/settings'), {
					name: 'php_interpreter',
					value: value ? 'true' : 'false'
				})
			} catch (error) {
				showError(error.response.data.error)
				this.usePhpInterpreter = !value
			}
		}
	},
}

</script>
<style scoped lang="scss">
.empty-content {
	margin-top: 0;
}

.script-cards {
	margin-top: 16px
}

.section {
	border-top: 1px solid var(--color-border);
	margin-top: 30px;
}
</style>
