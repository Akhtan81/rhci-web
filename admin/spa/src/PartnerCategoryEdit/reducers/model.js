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

const hasChildren = (prev = false, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.hasChildren !== undefined) {
                return action.payload.hasChildren
            }
            return false
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
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.category !== undefined) {
                return action.payload.category
            }

            if (action.payload.type !== undefined) {
                return null
            }

            return prev
        default:
            return prev
    }
}

const unit = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.unit !== undefined) {
                return action.payload.unit
            }
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.unit !== undefined) {
                return action.payload.unit
            }
            return prev
        default:
            return prev
    }
}

const type = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.category !== undefined) {
                return action.payload.category.type.key
            }
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.type !== undefined) {
                return action.payload.type
            }
            return prev
        default:
            return prev
    }
}

const minAmount = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.minAmount !== undefined) {
                return action.payload.minAmount
            }
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.minAmount !== undefined) {
                return action.payload.minAmount
            }
            return prev
        default:
            return prev
    }
}

const price = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.price !== undefined) {
                return priceFormat(action.payload.price)
            }
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.price !== undefined) {
                return action.payload.price
            }
            return prev
        default:
            return prev
    }
}

const bidirectional = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.bidirectional !== undefined) {
                return action.payload.bidirectional
            }
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.bidirectional !== undefined) {
                return action.payload.bidirectional
            }
            return prev
        default:
            return prev
    }
}

export default combineReducers({
    id,
    createdAt,
    hasChildren,
    category,
    minAmount,
    unit,
    type,
    price,
    bidirectional,
})