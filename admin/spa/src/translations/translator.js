import messages from './messages.en.js'

const trans = (key) => messages[key] !== undefined ? messages[key] : key

export default trans
