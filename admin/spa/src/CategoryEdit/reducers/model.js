import {combineReducers} from 'redux'
import keyBy from 'lodash/keyBy'
import * as Action from '../actions'

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

const translations = (prev = {}, action) => {
    switch (action.type) {
        case Action.ADD_TRANSLATION:

            const trans = action.payload

            return {
                ...prev,
                [trans.locale]: {
                    ...trans,
                    ...prev[trans.locale],
                }
            }

        case Action.MODEL_CHANGED:
            if (action.payload.translation !== undefined) {

                const trans = action.payload.translation

                return {
                    ...prev,
                    [trans.locale]: {
                        ...prev[trans.locale],
                        ...trans
                    }
                }
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.translations !== undefined) {
                return keyBy(action.payload.translations, 'locale')
            }
            return {}
        default:
            return prev
    }
}

const type = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.type !== undefined) {
                return action.payload.type
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.type !== undefined) {
                return action.payload.type
            }
            return null
        default:
            return prev
    }
}

const ordering = (prev = 0, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.ordering !== undefined) {
                return action.payload.ordering
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.ordering !== undefined) {
                return action.payload.ordering
            }
            return 0
        default:
            return prev
    }
}

const parent = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.type !== undefined) {
                return null
            }
            if (action.payload.parent !== undefined) {
                return action.payload.parent
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.parent !== undefined) {
                return action.payload.parent.id
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    id,
    ordering,
    translations,
    type,
    parent,
    createdAt,
})