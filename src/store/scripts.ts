import Vuex from 'vuex'
import { Script, defaultScript } from '../types/script'
import { api } from '../api/script'

export interface State {
	loadingScriptId: number;
	scripts: Script[];
	selectedScript: Script;
}

const getters = {
	getEnabledScripts(state: State) {
		return state.scripts
			? state.scripts.filter(s => s.enabled)
			: null
	},
}

const mutations = {
	setScripts(state: State, scripts: Script[]) {
		state.scripts = scripts
	},

	setSelectedScript(state: State, script: Script) {
		state.selectedScript = script
	},

	selectedToggleValue(state: State, value: string) {
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
	},
}

const actions = {
	async fetchScripts({ commit }) {
		commit('setScripts', await api.getScripts())
	},

	async saveScript({ dispatch, commit, state }) {
		const script = state.selectedScript
		if (script.id) {
			await api.updateScript(script)
		} else {
			const newScript = await api.createScript(script)
			commit('setSelectedScript', newScript)
		}
		dispatch('fetchScripts')
	},

	async deleteScript({ dispatch, commit, state }, script) {
		commit('clearAll')
		if (script.id) {
			await api.deleteScript(script)
		}
		dispatch('fetchScripts')
	},
}

export const store = new Vuex.Store({
	state: {
		loadingScriptId: null,
		scripts: null,
		selectedScript: null,
	} as State,
	getters,
	mutations,
	actions,
})
