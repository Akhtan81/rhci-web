import {combineReducers} from 'redux'
import * as Action from '../actions'
import * as PartnerAction from '../../Partner/actions'
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
            if (action.payload.createdAt !== undefined) {
                return action.payload.createdAt
            }
            return null
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
        case Action.MODEL_CHANGED:
            if (action.payload.country !== undefined) {
                return action.payload.country
            }
            return prev
        default:
            return prev
    }
}

const region = (prev = null, action) => {
    switch (action.type) {
        case PartnerAction.FETCH_COUNTRIES_SUCCESS:
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.region !== undefined) {
                return action.payload.region
            }
            return prev
        default:
            return prev
    }
}

const city = (prev = null, action) => {
    switch (action.type) {
        case PartnerAction.FETCH_COUNTRIES_SUCCESS:
        case PartnerAction.FETCH_REGIONS_SUCCESS:
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.city !== undefined) {
                return action.payload.city
            }
            return prev
        default:
            return prev
    }
}

const district = (prev = null, action) => {
    switch (action.type) {
        case PartnerAction.FETCH_COUNTRIES_SUCCESS:
        case PartnerAction.FETCH_REGIONS_SUCCESS:
        case PartnerAction.FETCH_CITIES_SUCCESS:
            return null
        case Action.MODEL_CHANGED:
            if (action.payload.district !== undefined) {
                return action.payload.district
            }
            return prev
        case Action.FETCH_SUCCESS:
            if (action.payload.district !== undefined) {
                return action.payload.district
            }
            return null
        default:
            return prev
    }
}

const originalDistrict = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.district !== undefined) {
                return action.payload.district
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    id,
    createdAt,
    user,
    country,
    region,
    city,
    district,
    originalDistrict,
})