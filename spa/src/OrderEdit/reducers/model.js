import {combineReducers} from 'redux'
import * as Action from '../actions'

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
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.createdAt !== undefined) {
                return action.payload.createdAt
            }
            return null
        default:
            return prev
    }
}

const user = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.user !== undefined) {
                return action.payload.user
            }
            return null
        default:
            return prev
    }
}

const partner = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.partner !== undefined) {
                return action.payload.partner
            }
            return null
        default:
            return prev
    }
}

const district = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.district !== undefined) {
                return action.payload.district
            }
            return null
        default:
            return prev
    }
}

const price = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.price !== undefined) {
                return action.payload.price
            }
            return null
        default:
            return prev
    }
}

const location = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.location !== undefined) {
                return action.payload.location
            }
            return null
        default:
            return prev
    }
}

const repeatable = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.repeatable !== undefined) {
                return action.payload.repeatable
            }
            return null
        default:
            return prev
    }
}

const status = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.status !== undefined) {
                return action.payload.status
            }
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.status !== undefined) {
                return action.payload.status
            }
            return prev
        default:
            return prev
    }
}

const updatedAt = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.updatedAt !== undefined) {
                return action.payload.updatedAt
            }
            return null
        default:
            return prev
    }
}

const updatedBy = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.updatedBy !== undefined) {
                return action.payload.updatedBy
            }
            return null
        default:
            return prev
    }
}

const scheduledAt = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.scheduledAt !== undefined) {
                return action.payload.scheduledAt
            }
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.scheduledAt !== undefined) {
                return action.payload.scheduledAt
            }
            return prev
        default:
            return prev
    }
}

const isScheduleConfirmed = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.isScheduleConfirmed !== undefined) {
                return action.payload.isScheduleConfirmed
            }
            return false
        case Action.MODEL_CHANGED:
            if (action.payload.isScheduleConfirmed !== undefined) {
                return action.payload.isScheduleConfirmed
            }
            return prev
        default:
            return prev
    }
}

const items = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.items !== undefined) {
                return action.payload.items
            }
            return []
        default:
            return prev
    }
}

const payments = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.payments !== undefined) {
                return action.payload.payments
            }
            return []
        default:
            return prev
    }
}

const messages = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.messages !== undefined) {
                return action.payload.messages
            }
            return []
        default:
            return prev
    }
}

export default combineReducers({
    id,
    status,
    createdAt,
    updatedAt,
    updatedBy,
    scheduledAt,
    isScheduleConfirmed,
    user,
    partner,
    district,
    price,
    location,
    repeatable,
    messages,
    items,
    payments,
})