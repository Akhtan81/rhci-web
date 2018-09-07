import {combineReducers} from 'redux'
import {LOGIN_SUCCESS} from '../../Login/actions'

const email = (state = AppParameters.user.email, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.email !== undefined) {
                return action.payload.user.email
            }
            return null
        default:
            return state
    }
}

const phone = (state = AppParameters.user.phone, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.phone !== undefined) {
                return action.payload.user.phone
            }
            return null
        default:
            return state
    }
}

const name = (state = AppParameters.user.isAdmin, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.name !== undefined) {
                return action.payload.user.name
            }
            return false
        default:
            return state
    }
}

const partner = (state = AppParameters.user.partner, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.partner !== undefined) {
                return action.payload.user.partner
            }
            return false
        default:
            return state
    }
}

const isAdmin = (state = AppParameters.user.isAdmin, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.isAdmin !== undefined) {
                return action.payload.user.isAdmin
            }
            return false
        default:
            return state
    }
}

export default combineReducers({
    name,
    phone,
    email,
    isAdmin,
    partner,
})
