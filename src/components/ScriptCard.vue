<template>
	<ListItem
		:title="this.script.title"
		details="Enabled"
		:active="this.script.isEnabled"
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
import {useScripts} from "../store/scripts"
import {computed, defineComponent} from "@vue/composition-api";
import {Script} from "../types/script";

export default defineComponent({
	name: 'ScriptCard',
	components: {
		ListItem, ActionButton
	},

	setup(props) {
		const scriptsStore = useScripts();
		scriptsStore.loadScripts()

		return {
			script: computed( (): Script => scriptsStore.getScriptById(props.id) ),
			isLoading: computed((): boolean => scriptsStore.getLoadingScriptId === props.id),
			selectScript: function() { scriptsStore.selectScript(this.script); }
		}
	},

	props: {
		id: Number
	},
})
</script>

<style scoped>
.icon-loading {
	display: contents;
}
</style>
