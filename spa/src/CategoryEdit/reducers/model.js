import {combineReducers} from 'redux'
import * as Action from '../actions'
import {OrderTypes} from '../components/index'
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

const locale = (prev = AppParameters.locale, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.locale !== undefined) {
                return action.payload.locale
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.locale !== undefined) {
                return action.payload.locale
            }
            return AppParameters.locale
        default:
            return prev
    }
}

const name = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.name !== undefined) {
                return action.payload.name
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.name !== undefined) {
                return action.payload.name
            }
            return null
        default:
            return prev
    }
}

const initialType = OrderTypes[0].value;
const type = (prev = initialType, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.type !== undefined) {
                return action.payload.type
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.type !== undefined) {
                return action.payload.type
            }
            return initialType
        default:
            return prev
    }
}

const isSelectable = (prev = false, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.isSelectable !== undefined) {
                return action.payload.isSelectable
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.isSelectable !== undefined) {
                return action.payload.isSelectable
            }
            return false
        default:
            return prev
    }
}

const hasPrice = (prev = false, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.hasPrice !== undefined) {
                return action.payload.hasPrice
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.hasPrice !== undefined) {
                return action.payload.hasPrice
            }
            return false
        default:
            return prev
    }
}

const ordering = (prev = 0, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.ordering !== undefined) {
                return action.payload.ordering
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.ordering !== undefined) {
                return action.payload.ordering
            }
            return 0
        default:
            return prev
    }
}


const price = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
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

const parent = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.parent !== undefined) {
                return action.payload.parent
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.parent !== undefined) {
                return action.payload.parent.id
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    id,
    ordering,
    name,
    locale,
    type,
    isSelectable,
    hasPrice,
    price,
    parent,
    createdAt,
})