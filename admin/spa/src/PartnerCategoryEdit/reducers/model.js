import {combineReducers} from 'redux'
import * as Action from '../actions'
import {priceFormat} from "../../Common/utils";

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

const category = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.category !== undefined) {
                return action.payload.category
            }
            return AppParameters.locale
        default:
            return prev
    }
}

const price = (prev = null, action) => {
    switch (action.type) {
        case Action.CATEGORY_CHANGED:
            if (action.payload.price !== undefined) {
                return action.payload.price
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.price !== undefined) {
                return priceFormat(action.payload.price)
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    id,
    category,
    price,
    createdAt,
})