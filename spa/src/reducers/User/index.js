import {combineReducers} from 'redux'
import {LOGIN_FAILURE, LOGIN_SUCCESS} from '../../Login/actions'
import model from './model'

const isAuthenticated = (state = AppParameters.isAuthenticated, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            return true
        case LOGIN_FAILURE:
            return false
        default:
            return state
    }
}

export default combineReducers({
    isAuthenticated,
    model,
})
