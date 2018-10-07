import {combineReducers} from 'redux'
import user from './user'
import * as Action from "../actions";

const token = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.token !== undefined) {
                return action.payload.token
            }
            return prev
        default:
            return prev
    }
}

export default combineReducers({
    token,
    user,
})