import {combineReducers} from 'redux'
import * as Action from '../actions'
import user from './user'

const id = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
            if (action.payload.id !== undefined) {
                return action.payload.id
            }
            return null
        case Action.FETCH_SUCCESS:
            if (action.payload.id !== undefined) {
                return action.payload.id
            }
            return null
        default:
            return prev
    }
}

const createdAt = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
            if (action.payload.createdAt !== undefined) {
                return action.payload.createdAt
            }
            return null
        case Action.FETCH_SUCCESS:
            if (action.payload.createdAt !== undefined) {
                return action.payload.createdAt
            }
            return null
        default:
            return prev
    }
}

const country = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.country !== undefined) {
                return action.payload.country
            }
            return prev
        default:
            return prev
    }
}

const postalCodes = (prev = '', action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.postalCodes !== undefined) {
                return action.payload.postalCodes
            }
            return prev
        case Action.FETCH_SUCCESS:
            if (action.payload.postalCodes !== undefined) {
                return action.payload.postalCodes.map(item => item.postalCode).join(',')
            }
            return null
        default:
            return prev
    }
}

const requestedPostalCodes = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.requestedPostalCodes !== undefined) {
                return action.payload.requestedPostalCodes
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    id,
    createdAt,
    user,
    country,
    postalCodes,
    requestedPostalCodes,
})