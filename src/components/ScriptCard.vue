<template>
	<ListItem
		v-if="this.script"
		:title="this.script.title"
		:details="this.enabledText()"
		:force-display-actions="true"
		@click="this.editScript"
	>
		<template #subtitle>{{ script.description }}</template>
		<template #actions>
			<ActionButton icon="icon-rename" @click="editScript" :closeAfterClick="true">
				{{ t('Edit') }}
			</ActionButton>
			<ActionButton icon="icon-delete" @click="deleteScript" :closeAfterClick="true">
				{{ t('Delete') }}
			</ActionButton>
		</template>
	</ListItem>
</template>

<script lang="ts">
import ListItem from '@nextcloud/vue/dist/Components/ListItem'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import {Script} from '../types/script'
import {translate as t} from "../l10n";

export default {
	name: 'ScriptCard',
	components: {
		ListItem, ActionButton
	},

	methods: {
		t,
		enabledText() {
			return this.script.enabled ? t('Enabled') : t('Disabled');
		},
		editScript() {
			this.$emit('select', this.script)
		},
		deleteScript() {
			this.$emit('delete', this.script)
		}
	},

	props: {
		script: Object as () => Script
	},
}
</script>
