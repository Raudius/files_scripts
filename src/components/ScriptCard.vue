<template>
	<NcListItem v-if="script"
		:name="script.title"
		:details="enabledText()"
		:force-display-actions="true"
		@click="editScript">
		<template #subtitle>
			{{ script.description }}
		</template>
		<template #actions>
			<NcActionButton icon="icon-rename" :close-after-click="true" @click="editScript">
				{{ t('files_scripts', 'Edit') }}
			</NcActionButton>
			<NcActionButton :close-after-click="true" @click="duplicateScript">
				<template #icon>
					<ContentDuplicate :size="20" />
				</template>
				{{ t('files_scripts', 'Duplicate') }}
			</NcActionButton>
			<NcActionButton icon="icon-delete" :close-after-click="true" @click="deleteScript">
				{{ t('files_scripts', 'Delete') }}
			</NcActionButton>
		</template>
	</NcListItem>
</template>

<script lang="ts">
import { NcListItem, NcActionButton } from '@nextcloud/vue'
import { Script } from '../types/script'
import { translate as t } from '../l10n'
import ContentDuplicate from 'vue-material-design-icons/ContentDuplicate.vue'


export default {
	name: 'ScriptCard',
	components: {
		NcListItem,
		NcActionButton,
		ContentDuplicate,
	},

	props: {
		script: Object as () => Script,
	},

	methods: {
		t,
		enabledText() {
			return this.script.enabled ? t('files_scripts', 'Enabled') : t('files_scripts', 'Disabled')
		},
		editScript() {
			this.$emit('select', this.script)
		},
		duplicateScript() {
			this.$emit('duplicate', this.script)
		},
		deleteScript() {
			this.$emit('delete', this.script)
		},
	},
}
</script>
