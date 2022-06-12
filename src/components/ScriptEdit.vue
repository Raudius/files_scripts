<template>
	<div class="container-script-edit">
		<div>
			<input type="text"
				   class="input-script-name"
				   required
				   placeholder="Script name"
				   v-model="scriptTitle"
			>
		</div>
		<CodeMirror v-model="scriptProgram" class="editor" :options="cmOption" />
		<Button type="primary" @click="saveScript">
			<template #icon>
				<Save :size="20" />
			</template>
			Save
		</Button>
	</div>
</template>

<script lang="ts">
import Button from "@nextcloud/vue/dist/Components/Button";
import Save from "vue-material-design-icons/ContentSave.vue";

import 'codemirror/mode/lua/lua.js'
import 'codemirror/addon/edit/matchbrackets.js'
import 'codemirror/addon/hint/show-hint.js'
const CodeMirror = require('vue-codemirror').codemirror;

export default {
	name: 'ScriptEdit',
	components: {
		Button,
		Save,
		CodeMirror,
	},
	data() {
		return {
			cmOption: {
				tabSize: 4,
				styleActiveLine: true,
				lineNumbers: true,
				line: true,
				foldGutter: true,
				styleSelectedText: true,
				matchBrackets: true,
				showCursorWhenSelecting: true,
				mode: 'text/x-lua',
				theme: 'idea',
				extraKeys: { Ctrl: 'autocomplete' },
				hintOptions: {
					completeSingle: true,
				},
			},
		}
	},
	computed: {
		scriptTitle: {
			get() {
				return this.$store.state.selectedScript.title
			},
			set (value) {
				this.$store.commit('updateCurrentScript', {title: value})
			}
		},
		scriptProgram: {
			get() {
				return this.$store.state.selectedScript.program
			},
			set (value) {
				this.$store.commit('updateCurrentScript', {program: value})
			}
		}
	},

	methods: {
		saveScript() {
			this.$store.commit('saveScript')
		}
	}
}
</script>

<style scoped>
@import 'codemirror/lib/codemirror.css';
@import 'codemirror/lib/codemirror.css';
@import 'codemirror/theme/idea.css';
@import 'codemirror/theme/base16-light.css';
@import '../../css/codemirror.css';

.editor {
	height: 90%;
	margin-bottom: 10px;
	border: solid 1px slategray;
}
.container-script-edit {
	height: 90%;
	margin: 10px;
}
.input-script-name {
	width: 100%;
	font-size: large;
}
</style>
