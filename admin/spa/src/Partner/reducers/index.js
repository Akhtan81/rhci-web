import {combineReducers} from 'redux'
import * as Action from '../actions'

const initialFilter = {statuses: 'created,approved'}
const filter = (prev = initialFilter, action) => {
    switch (action.type) {
        case Action.FILTER_CLEAR:
            return initialFilter
        case Action.FILTER_CHANGED:
            return {
                ...prev,
                ...action.payload
            }
        default:
            return prev
    }
}

const page = (prev = 1, action) => {
    switch (action.type) {
        case Action.FILTER_CLEAR:
            return 1
        case Action.PAGE_CHANGED:
            return action.payload
        case Action.FETCH_SUCCESS:
            return action.payload.page
        default:
            return prev
    }
}

const limit = (prev = 0, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            return action.payload.limit
        default:
            return prev
    }
}

const total = (prev = 0, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            return action.payload.total
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
    filter,
    page,
    limit,
    total,
    items,
    isLoading,
})
