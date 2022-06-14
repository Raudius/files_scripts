import Vuex, {ActionTree, GetterTree, MutationTree} from 'vuex'
import {Script} from "../types/script";
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
	async setScripts(state: State, scripts: Script[]) {
		state.scripts = scripts
	},

	addScript(state: State) {
		const id = Math.floor(Math.random()*1000);
		const title = 'Script #' + id
		state.scripts[id] = {
			id: id,
			title: title
		} as Script;
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
			script.description = ''; // TODO: remove this after v-binding
			script.enabled = true; // TODO: remove this after v-binding
			await axios.post(generateUrl('/apps/files_scripts/scripts'), state.selectedScript)
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
