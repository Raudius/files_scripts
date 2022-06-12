import Vuex, {ActionTree, GetterTree, MutationTree} from 'vuex'
import {Script, ScriptDescriptor} from "../types/script";


export interface State {
	loadingScriptId: number;
	scripts: { [index: number]: ScriptDescriptor };
	selectedScript: Script;
}

const getters = <GetterTree<State, any>>{
	getScriptById(state: State) {
		return (scriptId) => state.scripts[scriptId] ?? null;
	},
};

const mutations = <MutationTree<State>> {
	async loadScripts(state: State, scripts: ScriptDescriptor[]) {
		state.scripts = scripts
	},

	addScript(state: State) {
		const id = Math.floor(Math.random()*1000);
		const title = 'Script #' + id
		state.scripts[id] = {
			id: id,
			title: title
		} as ScriptDescriptor;
	},

	setLoadingScriptId(state: State, scriptId: number) {
		state.loadingScriptId = scriptId
	},

	setSelectedScript(state: State, script: Script) {
		state.selectedScript = script
	},

	newScript(state: State) {
		state.selectedScript = { id: null, title: '', program: '' } as Script
	},
	clearSelected(state: State) {
		state.selectedScript = null
		state.loadingScriptId = null;
	},
	clearAll(state: State) {
		state.scripts = null;
		state.selectedScript = null
		state.loadingScriptId = null;
	},
	updateCurrentScript(state: State, newValues: Script) {
		state.selectedScript = {
			...state.selectedScript,
			title: newValues.title ?? state.selectedScript.title,
			program: newValues.program ?? state.selectedScript.program
		}
	},
	saveScript(state: State) {
		console.log('Save')
	}
}

const actions = <ActionTree<State, any>>{
	fetchScripts({ commit }) {
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

		setTimeout(() => {
			commit('loadScripts', scripts)
		}, 1000)
	},

	selectScript({ commit, state }, scriptDescriptor: ScriptDescriptor) {
		if (!scriptDescriptor || state.loadingScriptId === scriptDescriptor.id || state.selectedScript?.id === scriptDescriptor.id) {
			return
		}
		commit('setLoadingScriptId', scriptDescriptor.id)

		setTimeout(() => {
			const script = {
				...scriptDescriptor,
				program: '-- ' + scriptDescriptor.title + `
local foo = "bar"
function helloWorld()
	print("Hello, world!")
end`
			} as Script;

			if (script.id === state.loadingScriptId) {
				commit('setSelectedScript', script)
			}
		}, 600)
	}
};

export const scripts = new Vuex.Store({
	state: {
		loadingScriptId: null,
		scripts:  null,
		selectedScript:  null,
	} as State,
	getters: getters,
	mutations: mutations,
	actions: actions
})
