import {combineReducers} from 'redux'
import * as Action from '../actions'
import user from './user'
import location from './location'

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

const status = (prev = null, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.status !== undefined) {
                return action.payload.status
            }
            return null
        default:
            return prev
    }
}

const country = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.country !== undefined) {
                return action.payload.country
            }
            return prev
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

const postalCodesRecycling = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.postalCodesRecycling !== undefined) {
                return action.payload.postalCodesRecycling
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            let items

            if (action.payload.postalCodes !== undefined) {
                items = action.payload.postalCodes

                return items.filter(item => item.type === 'recycling')
                    .map(item => item.postalCode)
                    .join(',')
            }

            if (action.payload.requests !== undefined) {
                items = action.payload.requests

                return items.filter(item => item.type === 'recycling')
                    .map(item => item.postalCode)
                    .join(',')
            }

            return null
        default:
            return prev
    }
}

const postalCodesbusybee = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.postalCodesbusybee !== undefined) {
                return action.payload.postalCodesbusybee
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            let items

            if (action.payload.postalCodes !== undefined) {
                items = action.payload.postalCodes

                return items.filter(item => item.type === 'busybee')
                    .map(item => item.postalCode)
                    .join(',')
            }

            if (action.payload.requests !== undefined) {
                items = action.payload.requests

                return items.filter(item => item.type === 'busybee')
                    .map(item => item.postalCode)
                    .join(',')
            }

            return null
        default:
            return prev
    }
}

const postalCodesJunkRemoval = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.postalCodesJunkRemoval !== undefined) {
                return action.payload.postalCodesJunkRemoval
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            let items

            if (action.payload.postalCodes !== undefined) {
                items = action.payload.postalCodes

                return items.filter(item => item.type === 'junk_removal')
                    .map(item => item.postalCode)
                    .join(',')
            }

            if (action.payload.requests !== undefined) {
                items = action.payload.requests

                return items.filter(item => item.type === 'junk_removal')
                    .map(item => item.postalCode)
                    .join(',')
            }
            return null
        default:
            return prev
    }
}

const postalCodesShredding = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.postalCodesShredding !== undefined) {
                return action.payload.postalCodesShredding
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            let items

            if (action.payload.postalCodes !== undefined) {
                items = action.payload.postalCodes

                return items.filter(item => item.type === 'shredding')
                    .map(item => item.postalCode)
                    .join(',')
            }

            if (action.payload.requests !== undefined) {
                items = action.payload.requests

                return items.filter(item => item.type === 'shredding')
                    .map(item => item.postalCode)
                    .join(',')
            }
            return null
        default:
            return prev
    }
}

const postalCodesDonation = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.postalCodesDonation !== undefined) {
                return action.payload.postalCodesDonation
            }
            return prev
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            let items

            if (action.payload.postalCodes !== undefined) {
                items = action.payload.postalCodes

                return items.filter(item => item.type === 'donation')
                    .map(item => item.postalCode)
                    .join(',')
            }

            if (action.payload.requests !== undefined) {
                items = action.payload.requests

                return items.filter(item => item.type === 'donation')
                    .map(item => item.postalCode)
                    .join(',')
            }
            return null
        default:
            return prev
    }
}

const postalCodeOwners = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            return []
        case Action.FETCH_OWNERS_SUCCESS:
            if (action.payload.postalCodes !== undefined) {
                return action.payload.postalCodes
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

const categories = (prev = [], action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
        case Action.FETCH_SUCCESS:
            if (action.payload.categories !== undefined) {
                return action.payload.categories
            }
            return []
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
    postalCodesbusybee,
    postalCodesJunkRemoval,
    postalCodesShredding,
    postalCodesDonation,
    postalCodeOwners,
    requests,
    requestedCategories,
    categories,
    status,
})
