import {showError, showSuccess, showWarning, showInfo} from '@nextcloud/dialogs'

export enum MessageType {
	ERROR = 'error',
	WARNING = 'warning',
	INFO = 'info',
	SUCCESS = 'success'
}

export interface Message {
	message: string
	type: MessageType
}

export function getMessageType(type: any): MessageType {
	return MessageType[type] ?? MessageType.INFO
}

export function showMessage(message: Message) {
	const options = {
		timeout: 10000
	}

	switch (message.type) {
		case MessageType.ERROR:
			showError(message.message, options)
			return

		case MessageType.WARNING:
			showWarning(message.message, options)
			return;

		case MessageType.SUCCESS:
			showSuccess(message.message, options)
			return;

		default:
			showInfo(message.message, options)
	}
}
