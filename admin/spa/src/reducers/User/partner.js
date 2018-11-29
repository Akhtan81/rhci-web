import {combineReducers} from 'redux'
import {LOGIN_SUCCESS} from '../../Login/actions'
import {CANCEL_SUBSCRIPTION_SUCCESS, UPDATE_SUBSCRIPTION_SUCCESS} from '../../ProfilePartner/actions'
import * as Action from "../../ProfilePartner/actions";

const initial = AppParameters.user.partner

const id = (state = initial.id, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.partner !== undefined) {
                if (action.payload.user.partner.id !== undefined) {
                    return action.payload.user.partner.id
                }
            }
            return null
        default:
            return state
    }
}

const provider = (state = initial.provider, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.partner !== undefined) {
                if (action.payload.user.partner.provider !== undefined) {
                    return action.payload.user.partner.provider
                }
            }
            return null
        default:
            return state
    }
}

const subscription = (state = AppParameters.subscription, action) => {
    switch (action.type) {
        case UPDATE_SUBSCRIPTION_SUCCESS:
            return action.payload
        case CANCEL_SUBSCRIPTION_SUCCESS:
            if (action.payload && action.payload.length > 0) {
                return action.payload[0]
            }
            return state
        default:
            return state
    }
}

const hasCard = (prev = true, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.partner !== undefined) {
                return action.user.partner.hasCard
            }
            return null
        default:
            return prev
    }
}

const hasAccount = (prev = true, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.partner !== undefined) {
                return action.user.partner.hasAccount
            }
            return null
        default:
            return prev
    }
}

const hasCustomer = (prev = true, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.partner !== undefined) {
                return action.user.partner.hasCustomer
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    id,
    provider,
    subscription,
    hasCard,
    hasAccount,
    hasCustomer,
})
