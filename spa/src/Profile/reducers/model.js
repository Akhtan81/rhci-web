import {combineReducers} from 'redux'
import * as Action from '../actions'

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
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.country !== undefined) {
                return action.payload.country
            }
            return null
        default:
            return prev
    }
}

const postalCodes = (prev = '', action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
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
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.requestedPostalCodes !== undefined) {
                return action.payload.requestedPostalCodes
            }
            return null
        default:
            return prev
    }
}

const user = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.user !== undefined) {
                return action.payload.user
            }
            return null
        default:
            return prev
    }
}

const provider = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.provider !== undefined) {
                return action.payload.provider
            }
            return null
        default:
            return prev
    }
}

const accountId = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.accountId !== undefined) {
                return action.payload.accountId
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.accountId !== undefined) {
                return action.payload.accountId
            }
            return null
        default:
            return prev
    }
}

const hasAccount = (prev = false, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            return action.payload.accountId !== undefined
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
    hasAccount,
    provider,
    accountId,
})