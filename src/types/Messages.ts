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
	switch (message.type) {
		case MessageType.ERROR:
			showError(message.message)
			return

		case MessageType.WARNING:
			showWarning(message.message)
			return;

		case MessageType.SUCCESS:
			showSuccess(message.message)
			return;

		default:
			showInfo(message.message)
	}
}
