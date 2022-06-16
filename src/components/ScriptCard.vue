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
				Edit
			</ActionButton>
			<ActionButton icon="icon-delete" @click="deleteScript" :closeAfterClick="true">
				Delete
			</ActionButton>
		</template>
	</ListItem>
</template>

<script lang="ts">
import ListItem from '@nextcloud/vue/dist/Components/ListItem'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'

export default {
	name: 'ScriptCard',
	components: {
		ListItem, ActionButton
	},

	computed: {
		script: function () {
			return this.$store.getters.getScriptById(this.id)
		}
	},

	methods: {
		editScript() {
			this.script && this.$store.commit('setSelectedScript', this.script)
		},
		deleteScript() {
			this.script && this.$store.dispatch('deleteScript', this.script)
		},
		enabledText() {
			return this.script.enabled ? 'Enabled' : 'Disabled';
		}
	},

	props: {
		id: Number
	},
}
</script>
