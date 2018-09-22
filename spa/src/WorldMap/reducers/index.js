import {combineReducers} from 'redux'
import * as Action from '../actions'

const orders = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            return action.payload.items
        default:
            return prev
    }
}

const initialFilter = {
    // statuses: 'failed'
}
const filter = (prev = initialFilter, action) => {
    switch (action.type) {
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

const activeMarker = (prev = null, action) => {
    switch (action.type) {
        case Action.SET_ACTIVE_MARKER:
            return action.payload
        case Action.FETCH_BEFORE:
            return null
        default:
            return prev
    }
}

export default combineReducers({
    activeMarker,
    filter,
    orders,
    isLoading,
})


