import {combineReducers} from 'redux'
import * as Action from '../actions'

const id = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.id !== undefined) {
                return action.payload.id
            }
            return null
        default:
            return prev
    }
}

const email = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.user.email !== undefined) {
                return action.payload.user.email
            }
            return null
        default:
            return prev
    }
}

const phone = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.user.phone !== undefined) {
                return action.payload.user.phone
            }
            return null
        default:
            return prev
    }
}

const name = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.user.name !== undefined) {
                return action.payload.user.name
            }
            return null
        default:
            return prev
    }
}

const avatar = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.user.avatar !== undefined) {
                return action.payload.user.avatar
            }
            return null
        default:
            return prev
    }
}

const password = (prev = null, action) => {
    switch (action.type) {
        default:
            return prev
    }
}

const password2 = (prev = null, action) => {
    switch (action.type) {
        default:
            return prev
    }
}

const isActive = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.user.isActive !== undefined) {
                return action.payload.user.isActive
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    id,
    email,
    phone,
    name,
    avatar,
    password,
    password2,
    isActive,
})