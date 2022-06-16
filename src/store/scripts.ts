import Vuex, {ActionTree, GetterTree, MutationTree} from 'vuex'
import {Script, defaultScript} from "../types/script";
import axios from "@nextcloud/axios"
import {generateUrl} from "@nextcloud/router";

export interface State {
	loadingScriptId: number;
	scripts: { [index: number]: Script };
	selectedScript: Script;
}

const getters = <GetterTree<State, any>>{
	getScriptById(state: State) {
		return (scriptId) => state.scripts[scriptId] ?? null;
	},
	getScripts(state: State) {
		return state.scripts ? Object.values(state.scripts) : null;
	}
};

const mutations = <MutationTree<State>> {
	setScripts(state: State, scripts: Script[]) {
		state.scripts = scripts
	},

	setSelectedScript(state: State, script: Script) {
		state.selectedScript = script
	},

	selectedToggleEnabled(state: State) {
		if (state.selectedScript) {
			state.selectedScript.enabled = !state.selectedScript.enabled;
		}
	},

	newScript(state: State) {
		state.selectedScript = defaultScript()
	},

	clearSelected(state: State) {
		state.selectedScript = null
	},

	clearAll(state: State) {
		state.scripts = null;
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

const actions = <ActionTree<State, any>>{
	async fetchScripts({ commit }) {
		commit('clearAll')
		const result = (await axios.get(generateUrl('/apps/files_scripts/scripts'))).data as Script[]
		const scripts = {}
		result.forEach((script) => {
			scripts[script.id] = script
		})
		commit('setScripts', scripts)
	},

	async saveScript({dispatch, state}) {
		const script = state.selectedScript
		if (script.id) {
			await axios.put(generateUrl('/apps/files_scripts/scripts/' + script.id), state.selectedScript)
		} else {
			await axios.post(generateUrl('/apps/files_scripts/scripts'), state.selectedScript)
		}
		dispatch('fetchScripts')
	},

	async deleteScript({dispatch, commit, state}, script) {
		commit('clearAll')
		if (script.id) {
			await axios.delete(generateUrl('/apps/files_scripts/scripts/' + script.id))
		}
		dispatch('fetchScripts')
	},

	async runScript({dispatch}, payload: { script: Script, files: any[] }) {
		const script = payload.script
		return await axios.post(generateUrl('/apps/files_scripts/run/' + script.id), {files: payload.files})
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
