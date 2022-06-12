<template>
	<ListItem
		v-if="this.script"
		:title="this.script.title"
		details="Enabled"
		:active="this.isLoading"
		:force-display-actions="true"
		@click="this.selectScript"
	>
		<template #extra>
			<div v-if="isLoading" class="icon-loading"></div>
		</template>
		<template #subtitle>TODO: description goes here</template>
		<template #actions>
			<ActionButton icon="icon-toggle">
				Enable
			</ActionButton>
			<ActionButton icon="icon-delete">
				Delete
			</ActionButton>
		</template>
	</ListItem>
</template>

<script lang="ts">
import ListItem from '@nextcloud/vue/dist/Components/ListItem'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import {mapGetters, mapState} from "vuex";

export default {
	name: 'ScriptCard',
	components: {
		ListItem, ActionButton
	},

	computed: {
		...mapGetters([
			'getScriptById',
		]),
		...mapState({
			loadingScriptId: 'loadingScriptId'
		}),
		isLoading: function (): boolean {
			return this.loadingScriptId === this.id;
		},
		script: function () {
			return this.getScriptById(this.id)
		}
	},

	methods: {
		selectScript() {
			this.script && this.$store.dispatch('selectScript', this.script)
		}
	},

	props: {
		id: Number
	},
}
</script>

<style scoped>
.icon-loading {
	display: contents;
}
</style>
