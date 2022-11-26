<template>
	<div>
		<NcTextField
			:label="label"
			:label-visible="true"
			:value.sync="input"
			:showTrailingButton="true"
			trailingButtonIcon="arrowRight"
			@trailing-button-click="insertValue"
			v-on:keyup.enter="insertValue"
		/>
		<ol>
			<li v-for="(option, idx) in this.values" :key="idx">
				{{ option }}
			</li>
		</ol>
	</div>
</template>

<script lang="ts">
import { NcTextField } from '@nextcloud/vue'

export default {
	name: 'MultiInput',
	model: {
		prop: 'values',
		event: 'change'
	},
	components: {
		NcTextField
	},
	props: {
		label: String,
		values: {
			type: Array,
			default: () => []
		}
	},
	data() {
		return {
			input: ''
		}
	},
	methods: {
		insertValue() {
			if (this.input === '' || this.values.includes(this.input)) {
				return;
			}
			this.values.push(this.input)
			this.input = ''
		}
	},
}
</script>

<style scoped>
ol {
	font-weight: normal;
	list-style-type: decimal;
	list-style-position: inside;
}
ol:before {
	font-weight:bold;
}
</style>
