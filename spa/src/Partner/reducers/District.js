import {combineReducers} from 'redux'
import * as Action from '../actions'

const items = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_DISTRICTS_SUCCESS:
            return action.payload.items
        default:
            return prev
    }
}

const isLoading = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_DISTRICTS_SUCCESS:
        case Action.FETCH_DISTRICTS_FAILURE:
            return false
        case Action.FETCH_DISTRICTS_BEFORE:
            return true
        default:
            return prev
    }
}

export default combineReducers({
    items,
    isLoading,
})
