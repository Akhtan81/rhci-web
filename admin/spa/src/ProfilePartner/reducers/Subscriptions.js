import {combineReducers} from 'redux'
import * as Action from '../actions'


const isLoading = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_SUBSCRIPTIONS_SUCCESS:
        case Action.FETCH_SUBSCRIPTIONS_FAILURE:
        case Action.UPDATE_SUBSCRIPTION_FAILURE:
        case Action.UPDATE_SUBSCRIPTION_SUCCESS:
        case Action.CANCEL_SUBSCRIPTION_SUCCESS:
        case Action.CANCEL_SUBSCRIPTION_FAILURE:
            return false
        case Action.FETCH_SUBSCRIPTIONS_BEFORE:
        case Action.UPDATE_SUBSCRIPTION_BEFORE:
        case Action.CANCEL_SUBSCRIPTION_BEFORE:
            return true
        default:
            return prev
    }
}

const items = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUBSCRIPTIONS_SUCCESS:
            return action.payload.items
        default:
            return prev
    }
}


export default combineReducers({
    isLoading,
    items,
})

