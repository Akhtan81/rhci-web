let messages = {};
import messagesEn from './messages.en.js'
import messagesRu from './messages.ru.js'
import messagesKz from './messages.kz.js'

switch (AppParameters.locale) {
    case 'ru':
        messages = messagesRu
        break;
    case 'en':
        messages = messagesEn
        break;
    case 'kz':
        messages = messagesKz
        break;
    default:
        throw 'Unknown locale ' + AppParameters.locale;

}

const trans = (key) => messages[key] !== undefined ? messages[key] : key

export default trans
