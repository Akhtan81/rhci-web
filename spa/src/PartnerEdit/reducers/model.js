import {combineReducers} from 'redux'
import * as Action from '../actions'
import user from './user'
import location from './location'
import keyBy from "lodash/keyBy";
import {cid} from "../../Common/utils";

const id = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
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

const status = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.status !== undefined) {
                return action.payload.status
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

const initialCodes = keyBy([
    {
        cid: cid(),
        postalCode: null,
        type: null,
    }
], 'cid')

const postalCodes = (prev = initialCodes, action) => {
    let state
    switch (action.type) {
        case Action.MODEL_CHANGED:

            if (action.payload.request === undefined) {
                return prev
            }

            const id = action.payload.request.cid

            if (id === undefined) {
                return prev
            }

            state = {...prev}

            return {
                ...prev,
                [id]: {
                    ...prev[id],
                    ...action.payload.request
                }
            }

        case Action.REMOVE_POSTAL_CODE:
            state = {...prev}

            delete state[action.payload.cid]

            return state
        case Action.ADD_POSTAL_CODE:
            return {
                ...prev,
                [action.payload.cid]: action.payload
            }
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            let items

            if (action.payload.postalCodes !== undefined) {

                items = action.payload.postalCodes

                if (items.length > 0) {
                    return keyBy(items.map(item => {
                        item.cid = cid()
                        return item
                    }), 'cid')
                }
            }

            if (action.payload.requests !== undefined) {
                items = action.payload.requests

                if (items.length > 0) {
                    return keyBy(items.map(item => {
                        item.cid = cid()
                        return item
                    }), 'cid')
                }
            }

            return initialCodes
        default:
            return prev
    }
}

const requests = (prev = [], action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.requests !== undefined) {
                return action.payload.requests
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
    location,
    country,
    postalCodes,
    requests,
    status,
})