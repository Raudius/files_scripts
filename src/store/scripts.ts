import Vuex, {ActionTree, GetterTree, MutationTree} from 'vuex'
import {Script, defaultScript} from "../types/script";
import {api} from "../api/script";

export interface State {
	loadingScriptId: number;
	scripts: { [index: number]: Script };
	selectedScript: Script;
}

const getters = <GetterTree<State, any>>{
	getScriptById(state: State) {
		return (scriptId) => state.scripts[scriptId] ?? null
	},
	getScripts(state: State) {
		return state.scripts ? Object.values(state.scripts) : null
	},
	getEnabledScripts(state: State) {
		if (!state.scripts) {
			return null
		}

		return Object.values(state.scripts).filter(s => s.enabled)
	}
};

const mutations = <MutationTree<State>> {
	setScripts(state: State, scripts: Script[]) {
		state.scripts = scripts
	},

	setSelectedScript(state: State, script: Script) {
		state.selectedScript = script
	},

	selectedToggleValue(state: State,  value: string) {
		if (state.selectedScript && state.selectedScript.hasOwnProperty(value)) {
			state.selectedScript[value] = !state.selectedScript[value]
		}
	},

	newScript(state: State) {
		state.selectedScript = defaultScript()
	},

	clearSelected(state: State) {
		state.selectedScript = null
	},

	clearAll(state: State) {
		state.scripts = null
		state.selectedScript = null
	},

	updateCurrentScript(state: State, newValues: Script) {
		state.selectedScript = {
			...state.selectedScript,
			title: newValues.title ?? state.selectedScript.title,
			program: newValues.program ?? state.selectedScript.program,
			description: newValues.description ?? state.selectedScript.description,
			enabled: newValues.enabled ?? state.selectedScript.enabled,
		}
	}
}

const actions = <ActionTree<State, any>> {
	async fetchScripts({ commit }) {
		const result = await api.getScripts()
		const scripts = {}
		result.forEach((script) => {
			scripts[script.id] = script
		})
		commit('setScripts', scripts)
	},

	async saveScript({dispatch, commit, state}) {
		const script = state.selectedScript
		if (script.id) {
			await api.updateScript(script)
		} else {
			await api.createScript(script)
			commit('clearSelected')
		}
		dispatch('fetchScripts')
	},

	async deleteScript({dispatch, commit, state}, script) {
		commit('clearAll')
		if (script.id) {
			await api.deleteScript(script);
		}
		dispatch('fetchScripts')
	},

	async runScript({dispatch}, payload: { script: Script, files: any[] }) {
		return await api.runScript(payload.script, payload.files);
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
