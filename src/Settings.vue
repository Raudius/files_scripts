<template>
	<div>
		<Modal v-if="showModal" size="full" @close="clearSelected">
			<ScriptEdit />
		</Modal>
		<div class="section">
			<h2>Custom actions</h2>
			<div class="settings-hint">
				Here you can define custom actions which users may perform on their files. Actions are small Lua scripts that can create, modify, and/or delete files programatically.<br>
				For more details on how you can create actions visit the documentation. abc
			</div>
		</div>

		<div class="section">
			<Button type="primary" @click="newScript">
				<template #icon>
					<Plus :size="20" />
				</template>
				Create custom action
			</Button>

			<ul v-for="script in scripts" class="script-cards">
				<ScriptCard :id="script.id" :title="script.title"></ScriptCard>
			</ul>

			<div v-if="isLoading" class="icon-loading"></div>
			<EmptyContent v-if="scripts.length === 0" class="empty-content">
				No actions
				<template #icon>
					<FileCog />
				</template>
				<template #desc>No custom actions exist.</template>
			</EmptyContent>
		</div>
	</div>
</template>

<script lang="ts">
import Plus from 'vue-material-design-icons/Plus.vue'
import FileCog from 'vue-material-design-icons/FileCog.vue'
import Save from 'vue-material-design-icons/ContentSave.vue'
import Button from '@nextcloud/vue/dist/Components/Button'
import EmptyContent from '@nextcloud/vue/dist/Components/EmptyContent'
import Modal from '@nextcloud/vue/dist/Components/Modal'
import ScriptEdit from './components/ScriptEdit.vue';
import ScriptCard from './components/ScriptCard.vue';

import {defineComponent, computed} from '@vue/composition-api'
import {useScripts} from "./store/scripts";
import {ScriptDescriptor} from "./types/script";

export default defineComponent({
	name: 'Settings',
	components: {
		Plus,
		Button,
		FileCog,
		Save,
		EmptyContent,
		Modal,
		ScriptEdit,
		ScriptCard
	},

	setup() {
		const scriptsStore = useScripts();

		console.log('setup');

		return {
			loading: true,
			scripts: computed(() => scriptsStore.getScripts),
			showModal: computed( () => {  return !!scriptsStore.getSelectedScript }),
			clearSelected: () => scriptsStore.clearSelected(),
			newScript: scriptsStore.newScript,
		}
	}
});
</script>
<style scoped>
.section {
	border-bottom: 1px solid var(--color-border);
}
.empty-content {
	margin-top: 0;
}
.script-cards {
	margin-top: 16px
}
</style>
