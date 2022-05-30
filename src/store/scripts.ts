import {defineStore} from "pinia";
import {Script, ScriptDescriptor} from "../types/script";

interface ScriptsStore {
	scripts: ScriptDescriptor[],
	selectedScript: Script|null,
	loadingScriptId: number,
}

export const useScripts = defineStore('actions', {
	state: () => {
		return {
			scripts: {},
			selectedScript: null,
			loadingScriptId: null
		} as ScriptsStore
	},
	actions: {
		async loadScripts() {
			const scripts = {
				1: {
					id: 1,
					title: 'Merge PDFs',
					isEnabled: true,
				} as ScriptDescriptor,
				2: {
					id: 2,
					title: 'Create report',
					isEnabled: false,
				} as ScriptDescriptor
			};

			return new Promise(function(resolve) {
				setTimeout(resolve, 1000);
			});
		},
		addScript() {
			const id = Math.floor(Math.random()*1000);
			const title = 'Script #' + id
			this.scripts[id] = {
				id: id,
				title: title
			} as ScriptDescriptor;
		},
		async selectScript(script: ScriptDescriptor) {
			if (!script || this.loadingScriptId === script.id || this.selectedScript?.id === script.id) {
				return
			}

			this.loadingScriptId = script.id
			setTimeout(() => {
				script = {
					...script,
					program: '-- ' + script.title + `
local foo = "bar"
function helloWorld()
	print("Hello, world!")
end`
				} as Script;

				if (script.id === this.loadingScriptId) {
					this.selectedScript = script
				}
			}, 1000)
		},
		newScript() {
			this.selectedScript = { id: null, title: '', program: '' } as Script
		},
		clearSelected() {
			this.selectedScript = null
			this.loadingScriptId = null;
		},
		saveScript(script, program) {
			if (script.id) {

			}
		}
	},
	getters: {
		getScripts() {
			return Object.values(this.scripts);
		},
		getScriptById() {
			return (scriptId) => this.scripts[scriptId] ?? null;
		},
		getSelectedScript() {
			return this.selectedScript;
		},
		getLoadingScriptId() {
			return this.loadingScriptId
		}
	}
})
