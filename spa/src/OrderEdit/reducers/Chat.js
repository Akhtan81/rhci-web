import {combineReducers} from 'redux'
import * as Action from '../actions'

const messages = (prev = [], action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            let messages = []

            const model = action.payload

            if (model.message)
                messages.push(model.message)

            model.items.forEach(item => {
                if (item.message) {
                    messages.push(item.message)
                }
            })

            return messages
        default:
            return prev
    }
}

export default combineReducers({
    messages,
})
