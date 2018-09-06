import {combineReducers} from 'redux'

const login = (state = AppParameters.user.login, action) => {
    switch (action.type) {
        default:
            return state
    }
}

const isAdmin = (state = AppParameters.user.isAdmin, action) => {
    switch (action.type) {
        default:
            return state
    }
}

const partner = (state = AppParameters.user.partner, action) => {
    switch (action.type) {
        default:
            return state
    }
}

export default combineReducers({
    isAdmin,
    login,
    partner,
})
