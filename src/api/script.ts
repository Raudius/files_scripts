import axios from "@nextcloud/axios";
import {generateUrl} from "@nextcloud/router";
import {Script, ScriptInput} from "../types/script";

export const api = {
	async getScripts(): Promise<Script[]> {
		return (await axios.get(generateUrl('/apps/files_scripts/scripts'))).data;
	},

	async createScript(script: Script): Promise<any> {
		return (await axios.post(generateUrl('/apps/files_scripts/scripts'), script)).data;
	},

	async updateScript(script: Script): Promise<any> {
		return (await axios.put(generateUrl('/apps/files_scripts/scripts/' + script.id), script)).data
	},

	async deleteScript(script: Script): Promise<any> {
		return (await axios.delete(generateUrl('/apps/files_scripts/scripts/' + script.id))).data
	},

	async runScript(script: Script, files: any[]): Promise<any> {
		return (await axios.post(generateUrl('/apps/files_scripts/run/' + script.id), {files})).data
	},

	async getScriptInputs(scriptId: Number): Promise<ScriptInput[]> {
		return (await axios.get(generateUrl('/apps/files_scripts/scripts/' + scriptId + '/inputs'))).data
	},

	async updateScriptInputs(script, scriptInputs) {
		return (await axios.post(generateUrl('/apps/files_scripts/script_inputs/' + script.id), {scriptInputs})).data
	}
}
