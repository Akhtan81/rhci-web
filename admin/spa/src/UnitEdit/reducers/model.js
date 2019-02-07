import {combineReducers} from 'redux'
import * as Action from '../actions'
import keyBy from "lodash/keyBy";

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

export default combineReducers({
    id,
    translations,
    createdAt,
})