import {combineReducers} from 'redux'
import {LOGIN_SUCCESS} from '../../Login/actions'

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

const accountId = (state = initial.accountId, action) => {
    switch (action.type) {
        case LOGIN_SUCCESS:
            if (action.payload.user.partner !== undefined) {
                if (action.payload.user.partner.accountId !== undefined) {
                    return action.payload.user.partner.accountId
                }
            }
            return null
        default:
            return state
    }
}

export default combineReducers({
    id,
    provider,
    accountId,
})
