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

const type = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.type !== undefined) {
                return action.payload.type
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

const price = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.price !== undefined) {
                return action.payload.price
            }
            return prev
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.price !== undefined) {
                return priceFormat(action.payload.price)
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

const isScheduleApproved = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.isScheduleApproved !== undefined) {
                return action.payload.isScheduleApproved
            }
            return false
        case Action.MODEL_CHANGED:
            if (action.payload.isScheduleApproved !== undefined) {
                return action.payload.isScheduleApproved
            }
            return prev
        default:
            return prev
    }
}

const isPriceApproved = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.isPriceApproved !== undefined) {
                return action.payload.isPriceApproved
            }
            return false
        case Action.MODEL_CHANGED:
            if (action.payload.isPriceApproved !== undefined) {
                return action.payload.isPriceApproved
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

const message = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.message !== undefined) {
                return action.payload.message
            }
            return null
        default:
            return prev
    }
}

const statusReason = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_SUCCESS:
            if (action.payload.statusReason !== undefined) {
                return action.payload.statusReason
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    id,
    status,
    statusReason,
    createdAt,
    updatedAt,
    updatedBy,
    scheduledAt,
    isScheduleApproved,
    user,
    partner,
    price,
    isPriceApproved,
    location,
    repeatable,
    message,
    items,
    payments,
    type,
})
