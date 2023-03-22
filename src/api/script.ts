import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { inflateScriptInputOptions, Script, ScriptInput } from '../types/script'

export const api = {
	/**
	 * Get the scripts that are enabled for the current user.
	 */
	async getScripts(): Promise<Script[]> {
		return (await axios.get(generateUrl('/apps/files_scripts/scripts'))).data
	},

	/**
	 * Get all scripts (admin only).
	 */
	async getAllScripts(): Promise<Script[]> {
		return (await axios.get(generateUrl('/apps/files_scripts/scripts/all'))).data
	},

	async createScript(script: Script): Promise<any> {
		return (await axios.post(generateUrl('/apps/files_scripts/scripts'), script)).data
	},

	async updateScript(script: Script): Promise<any> {
		return (await axios.put(generateUrl('/apps/files_scripts/scripts/' + script.id), script)).data
	},

	async deleteScript(script: Script): Promise<any> {
		return (await axios.delete(generateUrl('/apps/files_scripts/scripts/' + script.id))).data
	},

	async runScript(script: Script, outputDirectory: string, inputs: ScriptInput[], files: any[]): Promise<any> {
		return (await axios.post(generateUrl('/apps/files_scripts/run/' + script.id), { outputDirectory, inputs, files })).data
	},

	async getScriptInputs(scriptId: Number): Promise<ScriptInput[]> {
		return (await axios.get(generateUrl('/apps/files_scripts/script_inputs/' + scriptId))).data
			.map((scriptInput: ScriptInput) => {
				return inflateScriptInputOptions(scriptInput)
			})
	},

	async updateScriptInputs(script: Script, scriptInputs: ScriptInput[]) {
		return (await axios.post(generateUrl('/apps/files_scripts/script_inputs/' + script.id), { scriptInputs })).data
	},
}
