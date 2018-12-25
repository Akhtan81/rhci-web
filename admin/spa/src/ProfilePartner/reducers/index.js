import {combineReducers} from 'redux'
import * as Action from '../actions'
import model from './model'
import Subscriptions from './Subscriptions'
import RequestedCodes from './RequestedCodes'
import RequestedCategories from './RequestedCategories'
import Categories from './Categories'
import OrderTypes from './OrderTypes'

const serverErrors = (prev = [], action) => {
    switch (action.type) {
        case Action.SAVE_FAILURE:
        case Action.CANCEL_SUBSCRIPTION_FAILURE:
        case Action.UPDATE_SUBSCRIPTION_FAILURE:
            if (action.payload.data.message !== undefined) {
                return [
                    action.payload.data.message
                ]
            }
            return []
        case Action.FETCH_SUCCESS:
        case Action.SAVE_BEFORE:
        case Action.CANCEL_SUBSCRIPTION_BEFORE:
        case Action.UPDATE_SUBSCRIPTION_BEFORE:
            return []
        default:
            return prev
    }
}

const isSaveSuccess = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.SAVE_BEFORE:
        case Action.SAVE_FAILURE:
        case Action.MODEL_CHANGED:
            return false
        case Action.SAVE_SUCCESS:
            return true
        default:
            return prev
    }
}

const isValid = (prev = false, action) => {
    switch (action.type) {
        case Action.VALIDATE_SUCCESS:
            return true
        case Action.FETCH_SUCCESS:
        case Action.VALIDATE_FAILURE:
            return false
        default:
            return prev
    }
}

const isLoading = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.FETCH_FAILURE:
        case Action.SAVE_SUCCESS:
        case Action.SAVE_FAILURE:
        case Action.UPDATE_SUBSCRIPTION_FAILURE:
        case Action.UPDATE_SUBSCRIPTION_SUCCESS:
        case Action.CANCEL_SUBSCRIPTION_SUCCESS:
        case Action.CANCEL_SUBSCRIPTION_FAILURE:
            return false
        case Action.SAVE_BEFORE:
        case Action.FETCH_BEFORE:
        case Action.UPDATE_SUBSCRIPTION_BEFORE:
        case Action.CANCEL_SUBSCRIPTION_BEFORE:
            return true
        default:
            return prev
    }
}

const initialValidator = {
    count: 0,
    messages: [],
    errors: {}
}
const validator = (prev = initialValidator, action) => {
    switch (action.type) {
        case Action.VALIDATE_SUCCESS:
        case Action.FETCH_SUCCESS:
            return initialValidator
        case Action.VALIDATE_FAILURE:
            return action.payload
        default:
            return prev
    }
}

const changes = (prev = {}, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            return {}
        case Action.MODEL_CHANGED:

            const changes = {...prev}

            Object.keys(action.payload).forEach(key => {
                changes[key] = true
            })

            return changes
        default:
            return prev
    }
}

export default combineReducers({
    RequestedCategories,
    RequestedCodes,
    Subscriptions,
    Categories,
    OrderTypes,
    model,
    isSaveSuccess,
    isValid,
    isLoading,
    validator,
    changes,
    serverErrors,
})

