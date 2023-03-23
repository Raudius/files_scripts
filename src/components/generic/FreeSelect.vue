<template>
	<NcSelect
		class="free-select"
		v-model="selected"
		@search="updateInput"
		:placeholder="label"
		:options="options"
		:multiple="true"
		:closeOnSelect="false"
		:tag-width="80"
		@option:selected="change"
		@option:deselected="change"
	>
		<template #no-options="{ search, searching, loading }">
			{{ hint }}
		</template>
	</NcSelect>
</template>

<script lang="ts">
import { NcSelect } from '@nextcloud/vue'

export default {
	name: 'FreeSelect',
	model: {
		prop: 'values',
		event: 'change'
	},
	components: {
		NcSelect
	},
	props: {
		label: String,
		hint: String,
		values: {
			type: Array,
			default: () => []
		}
	},
	data() {
		return {
			selected: [...this.values],
			options: []
		}
	},
	methods: {
		updateInput(input) {
			this.options = !!(input.trim()) ? [input] : []
		},
		change() {
			this.$emit("change", this.selected)
		}
	},
	watch: {
		values() {
			this.selected = this.values
		}
	}
}
</script>

<style scoped>
.free-select {
	width: 100%;
}
</style>
