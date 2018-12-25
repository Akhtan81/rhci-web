import {combineReducers} from 'redux'
import * as Action from '../actions'
import user from './user'

const id = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
            if (action.payload.id !== undefined) {
                return action.payload.id
            }
            return null
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

const country = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.country !== undefined) {
                return action.payload.country
            }
            return null
        default:
            return prev
    }
}

const location = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.address !== undefined) {
                return {
                    ...prev,
                    address: action.payload.address
                }
            }

            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.location !== undefined) {
                return action.payload.location
            }
            return null
        default:
            return prev
    }
}

const postalCodesRecycling = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            let items

            if (action.payload.postalCodes !== undefined) {
                items = action.payload.postalCodes

                return items.filter(item => item.type === 'recycling')
                    .map(item => item.postalCode)
            }

            if (action.payload.requests !== undefined) {
                items = action.payload.requests

                return items.filter(item => item.type === 'recycling')
                    .map(item => item.postalCode)
            }

            return []
        default:
            return prev
    }
}

const postalCodesJunkRemoval = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            let items

            if (action.payload.postalCodes !== undefined) {
                items = action.payload.postalCodes

                return items.filter(item => item.type === 'junk_removal')
                    .map(item => item.postalCode)
            }

            if (action.payload.requests !== undefined) {
                items = action.payload.requests

                return items.filter(item => item.type === 'junk_removal')
                    .map(item => item.postalCode)
            }
            return []
        default:
            return prev
    }
}

const postalCodesShredding = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            let items

            if (action.payload.postalCodes !== undefined) {
                items = action.payload.postalCodes

                return items.filter(item => item.type === 'shredding')
                    .map(item => item.postalCode)
            }

            if (action.payload.requests !== undefined) {
                items = action.payload.requests

                return items.filter(item => item.type === 'shredding')
                    .map(item => item.postalCode)
            }
            return []
        default:
            return prev
    }
}

const postalCodesDonation = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            let items

            if (action.payload.postalCodes !== undefined) {
                items = action.payload.postalCodes

                return items.filter(item => item.type === 'donation')
                    .map(item => item.postalCode)
            }

            if (action.payload.requests !== undefined) {
                items = action.payload.requests

                return items.filter(item => item.type === 'donation')
                    .map(item => item.postalCode)
            }
            return []
        default:
            return prev
    }
}

const requests = (prev = [], action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.requests !== undefined) {
                return action.payload.requests
            }
            return []
        default:
            return prev
    }
}

const requestedCategories = (prev = [], action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.requestedCategories !== undefined) {
                return action.payload.requestedCategories
            }
            return []
        default:
            return prev
    }
}

const provider = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.provider !== undefined) {
                return action.payload.provider
            }
            return null
        default:
            return prev
    }
}

const canManageJunkRemovalOrders = (prev = true, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            return action.payload.canManageJunkRemovalOrders
        default:
            return prev
    }
}

const canManageRecyclingOrders = (prev = true, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            return action.payload.canManageRecyclingOrders
        default:
            return prev
    }
}

const canManageDonationOrders = (prev = true, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            return action.payload.canManageDonationOrders
        default:
            return prev
    }
}

const canManageShreddingOrders = (prev = true, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            return action.payload.canManageShreddingOrders
        default:
            return prev
    }
}

const hasCard = (prev = true, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            return action.payload.hasCard
        default:
            return prev
    }
}

const hasCustomer = (prev = true, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            return action.payload.hasCustomer
        default:
            return prev
    }
}

const hasAccount = (prev = true, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            return action.payload.hasAccount
        default:
            return prev
    }
}

export default combineReducers({
    id,
    createdAt,
    user,
    location,
    country,
    postalCodesRecycling,
    postalCodesJunkRemoval,
    postalCodesShredding,
    postalCodesDonation,
    requests,
    requestedCategories,
    hasAccount,
    hasCustomer,
    hasCard,
    provider,
    canManageShreddingOrders,
    canManageDonationOrders,
    canManageRecyclingOrders,
    canManageJunkRemovalOrders,
})