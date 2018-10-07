import {combineReducers} from 'redux'
import * as Action from '../actions'

const login = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.login !== undefined) {
                return action.payload.login
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    login,
})