export interface ScriptDescriptor {
	id: number|null
	title: string
	isEnabled: boolean
}

export interface Script extends ScriptDescriptor{
	program: string
}
