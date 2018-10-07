import {combineReducers} from 'redux'
import * as Action from '../actions'

const id = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
            if (action.payload.location && action.payload.location.id !== undefined) {
                return action.payload.location.id
            }
            return null
        default:
            return prev
    }
}

const postalCode = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.postalCode !== undefined) {
                return action.payload.postalCode
            }
            return prev
        case Action.SAVE_SUCCESS:
            if (action.payload.location && action.payload.location.postalCode !== undefined) {
                return action.payload.location.postalCode
            }
            return null
        default:
            return prev
    }
}

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
    id,
    postalCode,
    address,
})