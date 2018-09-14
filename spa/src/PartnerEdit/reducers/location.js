import {combineReducers} from 'redux'
import * as Action from '../actions'

const id = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
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
        case Action.FETCH_SUCCESS:
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
        case Action.FETCH_SUCCESS:
            if (action.payload.location && action.payload.location.address !== undefined) {
                return action.payload.location.address
            }
            return null
        default:
            return prev
    }
}

const lng = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.lng !== undefined) {
                return action.payload.lng
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.location && action.payload.location.lng !== undefined) {
                return action.payload.location.lng
            }
            return null
        default:
            return prev
    }
}

const lat = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.lat !== undefined) {
                return action.payload.lat
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.location && action.payload.location.lat !== undefined) {
                return action.payload.location.lat
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
    lng,
    lat,
})