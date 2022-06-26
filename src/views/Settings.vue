<template>
	<div>
		<ScriptEdit />
		<SettingsSection :title="t('File actions')"
			:description="t('File actions are small Lua scripts that can create, modify, and/or delete files programatically. These actions may be triggered by users to be run on their files. Please read the documentation for more information.')"
			doc-url="https://github.com/Raudius/files_scripts/blob/master/docs/Admin.md">
			<div class="section">
				<Button type="primary" @click="newScript">
					<template #icon>
						<Plus :size="20" />
					</template>
					{{ t('New action') }}
				</Button>

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
				<EmptyContent v-if="scripts && scripts.length === 0" class="empty-content">
					{{ t('No actions') }}
					<template #icon>
						<FileCog />
					</template>
					<template #desc>
						{{ t('No file actions exist.') }}
					</template>
				</EmptyContent>
			</div>
		</SettingsSection>
	</div>
</template>

<script lang="ts">
import Plus from 'vue-material-design-icons/Plus.vue'
import FileCog from 'vue-material-design-icons/FileCog.vue'
import Button from '@nextcloud/vue/dist/Components/Button'
import EmptyContent from '@nextcloud/vue/dist/Components/EmptyContent'
import SettingsSection from '@nextcloud/vue/dist/Components/SettingsSection'
import ScriptEdit from '../components/ScriptEdit.vue'
import ScriptCard from '../components/ScriptCard.vue'
import { mapState } from 'vuex'
import { Script } from '../types/script'
import { translate as t } from '../l10n'

export default {
	name: 'Settings',
	components: {
		Button,
		SettingsSection,
		EmptyContent,
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
