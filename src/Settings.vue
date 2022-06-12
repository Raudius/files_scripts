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
			<EmptyContent v-if="scripts && scripts.length === 0" class="empty-content">
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
import Button from '@nextcloud/vue/dist/Components/Button'
import EmptyContent from '@nextcloud/vue/dist/Components/EmptyContent'
import Modal from '@nextcloud/vue/dist/Components/Modal'
import ScriptEdit from './components/ScriptEdit.vue';
import ScriptCard from './components/ScriptCard.vue';

import {defineComponent, computed} from '@vue/composition-api'
import {ScriptDescriptor} from "./types/script";
import {mapGetters, mapState} from 'vuex'
import {State} from "./store/scripts";
import Vue from "vue";


export default {
	name: 'Settings',
	components: {
		Button,
		EmptyContent,
		Modal,
		ScriptEdit,
		ScriptCard,
		Plus,
		FileCog
	},

	mounted() {
		this.$store.dispatch('fetchScripts')
	},

	computed: {
		...mapState({
			scripts: 'scripts',
			selectedScript: 'selectedScript',
		}),
		showModal: function (): boolean {
			return !!this.selectedScript
		},
		isLoading: function (): boolean {
			return this.scripts === null;
		}
	},

	methods: {
		clearSelected() {
			this.$store.commit('clearSelected')
		},
		newScript() {
			this.$store.commit('newScript')
		}
	}
}

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
