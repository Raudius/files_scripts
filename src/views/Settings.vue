<template>
	<div>
		<ScriptEdit />
		<SettingsSection
			title="Custom actions"
			description="Here you can define custom actions which users may perform on their files. Actions are small Lua scripts that can create, modify, and/or delete files programatically. For more details on how you can create actions visit the documentation. abc"
			doc-url="#"
		>
			<div class="section">
				<Button type="primary" @click="newScript">
					<template #icon>
						<Plus :size="20" />
					</template>
					Create custom action
				</Button>

				<ul class="script-cards">
					<ScriptCard v-for="script in scripts" v-bind:key="script.id" :id="script.id" :title="script.title"></ScriptCard>
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

		</SettingsSection>
	</div>
</template>

<script lang="ts">
import Plus from 'vue-material-design-icons/Plus.vue'
import FileCog from 'vue-material-design-icons/FileCog.vue'
import Button from '@nextcloud/vue/dist/Components/Button'
import EmptyContent from '@nextcloud/vue/dist/Components/EmptyContent'
import SettingsSection from '@nextcloud/vue/dist/Components/SettingsSection'
import ScriptEdit from '../components/ScriptEdit.vue';
import ScriptCard from '../components/ScriptCard.vue';
import {mapState} from 'vuex'

export default {
	name: 'Settings',
	components: {
		Button,
		SettingsSection,
		EmptyContent,
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
			selectedScript: 'selectedScript',
		}),
		scripts: function () {
			return this.$store.getters.getScripts
		},
		isLoading: function () {
			return this.scripts === null;
		}
	},

	methods: {
		newScript() {
			this.$store.commit('newScript')
		}
	}
}

</script>
<style scoped>
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
