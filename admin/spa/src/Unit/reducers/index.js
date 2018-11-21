import {combineReducers} from 'redux'
import * as Action from '../actions'

const initialFilter = {
    locale: typeof AppParameters !== 'undefined' ? AppParameters.locale : 'en'
}
const filter = (prev = initialFilter, action) => {
    switch (action.type) {
        case Action.FILTER_CHANGED:
            return {
                ...prev,
                ...action.payload
            }
        default:
            return prev
    }
}

const items = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            return action.payload.items;
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
    filter,
    items,
    isLoading,
})
