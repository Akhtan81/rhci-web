import {combineReducers} from 'redux'
import {LOGIN_FAILURE, LOGIN_SUCCESS, OFFLINE} from '../../Login/actions'
import model from './model'

const isAuthenticated = (state = AppParameters.isAuthenticated, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            return true
        case OFFLINE:
        case LOGIN_FAILURE:
            return false
        default:
            return state
    }
}

const timezone = (state = AppParameters.timezone, action) => {
    switch (action.type) {
        default:
            return state
    }
}

export default combineReducers({
    isAuthenticated,
    timezone,
    model,
})
