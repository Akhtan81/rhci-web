import {combineReducers} from 'redux'
import * as Action from '../actions'

const locale = (prev = AppParameters.locale, action) => {
    switch (action.type) {
        case Action.FILTER_CHANGED:

            if (action.payload.locale !== undefined) {
                return action.payload.locale
            }

            return prev
        default:
            return prev
    }
}

const filter = (prev = {type: 'junk_removal'}, action) => {
    switch (action.type) {
        case Action.FILTER_CHANGED:

            if (action.payload.type !== undefined) {
                return {
                    ...prev,
                    type: action.payload.type
                }
            }

            return prev
        default:
            return prev
    }
}

const items = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            return action.payload.items
        default:
            return prev
    }
}

const isLoading = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.FETCH_FAILURE:
            return false
        case Action.FETCH_BEFORE:
            return true
        default:
            return prev
    }
}

export default combineReducers({
    locale,
    filter,
    items,
    isLoading,
})
