import {combineReducers} from 'redux'
import * as Action from '../actions'

const address = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.address !== undefined) {
                return action.payload.address
            }
            return prev
        case Action.SAVE_SUCCESS:
            if (action.payload.location && action.payload.location.address !== undefined) {
                return action.payload.location.address
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    address,
})