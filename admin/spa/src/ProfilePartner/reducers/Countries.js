import {combineReducers} from 'redux'
import * as Action from '../actions'

const isLoading = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_COUNTRIES_SUCCESS:
        case Action.FETCH_COUNTRIES_FAILURE:
            return false
        case Action.FETCH_COUNTRIES_BEFORE:
            return true
        default:
            return prev
    }
}

const items = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_COUNTRIES_SUCCESS:
            return action.payload.items;
        default:
            return prev
    }
}

export default combineReducers({
    isLoading,
    items,
})

