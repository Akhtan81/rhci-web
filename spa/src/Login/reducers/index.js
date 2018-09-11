import {combineReducers} from 'redux'
import * as Action from '../actions'

const login = (prev = null, action) => {
    switch (action.type) {
        case Action.LOGIN_SUCCESS:
            return null
        case Action.LOGIN_CREDENTIALS_CHANGED:
            if (action.payload.login !== undefined) {
                return action.payload.login
            }

            return prev
        default:
            return prev
    }
}

const password = (prev = null, action) => {
    switch (action.type) {
        case Action.LOGIN_SUCCESS:
            return null
        case Action.LOGIN_CREDENTIALS_CHANGED:
            if (action.payload.password !== undefined) {
                return action.payload.password
            }

            return prev
        default:
            return prev
    }
}

const errors = (prev = [], action) => {
    switch (action.type) {
        case Action.LOGIN_BEFORE:
        case Action.LOGIN_SUCCESS:
        case Action.LOGIN_VALIDATE_SUCCESS:
            return []
        case Action.LOGIN_VALIDATE_FAILURE:
            return action.payload.errors
        case Action.LOGIN_FAILURE:
            if (action.payload.message !== undefined) {
                return [
                    action.payload.message
                ]
            }

            return []
        default:
            return prev
    }
}

const isValid = (prev = false, action) => {
    switch (action.type) {
        case Action.LOGIN_VALIDATE_FAILURE:
        case Action.LOGIN_SUCCESS:
            return false
        case Action.LOGIN_VALIDATE_SUCCESS:
            return true
        default:
            return prev
    }
}

const isLoading = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_CURRENT_USER_BEFORE:
        case Action.LOGIN_BEFORE:
            return true
        case Action.LOGIN_SUCCESS:
        case Action.LOGIN_FAILURE:
        case Action.FETCH_CURRENT_USER_SUCCESS:
        case Action.FETCH_CURRENT_USER_FAILURE:
            return false
        default:
            return prev
    }
}

export default combineReducers({
    login,
    password,
    errors,
    isValid,
    isLoading,
})
