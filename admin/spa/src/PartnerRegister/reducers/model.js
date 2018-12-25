import {combineReducers} from 'redux'
import * as Action from '../actions'
import user from './user'
import location from './location'
import keyBy from 'lodash/keyBy'
import {cid} from "../../Common/utils";

const id = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
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
        default:
            return prev
    }
}

const isAccepted = (prev = false, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.isAccepted !== undefined) {
                return action.payload.isAccepted
            }
            return prev
        default:
            return prev
    }
}

const initialRequest = keyBy([
    {
        cid: cid(),
        postalCode: null,
        type: null,
    }
], 'cid')

const requestedPostalCodes = (prev = initialRequest, action) => {
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
        default:
            return prev
    }
}

const initialCategoryRequest = keyBy([
    {
        cid: cid(),
        category: null,
    }
], 'cid')

const requestedCategories = (prev = initialCategoryRequest, action) => {
    let state
    switch (action.type) {
        case Action.MODEL_CHANGED:

            if (action.payload.requestedCategory === undefined) {
                return prev
            }

            const id = action.payload.requestedCategory.cid

            if (id === undefined) {
                return prev
            }

            state = {...prev}

            return {
                ...prev,
                [id]: {
                    ...prev[id],
                    ...action.payload.requestedCategory
                }
            }

        case Action.REMOVE_CATEGORY:
            state = {...prev}

            delete state[action.payload.cid]

            return state
        case Action.ADD_CATEGORY:
            return {
                ...prev,
                [action.payload.cid]: action.payload
            }

        default:
            return prev
    }
}

export default combineReducers({
    id,
    createdAt,
    user,
    location,
    requestedPostalCodes,
    requestedCategories,
    isAccepted,
})