import {combineReducers} from 'redux'
import * as Action from '../actions'

const password = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.password !== undefined) {
                return action.payload.password
            }
            return prev
        default:
            return prev
    }
}

const password2 = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.password2 !== undefined) {
                return action.payload.password2
            }
            return prev
        default:
            return prev
    }
}

export default combineReducers({
    password,
    password2,
})