<template>
	<ListItem v-if="script"
		:title="script.title"
		:details="enabledText()"
		:force-display-actions="true"
		@click="editScript">
		<template #subtitle>
			{{ script.description }}
		</template>
		<template #actions>
			<ActionButton icon="icon-rename" :close-after-click="true" @click="editScript">
				{{ t('Edit') }}
			</ActionButton>
			<ActionButton icon="icon-delete" :close-after-click="true" @click="deleteScript">
				{{ t('Delete') }}
			</ActionButton>
		</template>
	</ListItem>
</template>

<script lang="ts">
import ListItem from '@nextcloud/vue/dist/Components/ListItem'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import { Script } from '../types/script'
import { translate as t } from '../l10n'

export default {
	name: 'ScriptCard',
	components: {
		ListItem, ActionButton,
	},

	props: {
		script: Object as () => Script,
	},

	methods: {
		t,
		enabledText() {
			return this.script.enabled ? t('Enabled') : t('Disabled')
		},
		editScript() {
			this.$emit('select', this.script)
		},
		deleteScript() {
			this.$emit('delete', this.script)
		},
	},
}
</script>
