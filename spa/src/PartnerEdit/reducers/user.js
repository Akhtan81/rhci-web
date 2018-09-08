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
        case Action.MODEL_CHANGED:
            if (action.payload.email !== undefined) {
                return action.payload.email
            }
            return prev
        case Action.FETCH_SUCCESS:
            if (action.payload.user && action.payload.user.email !== undefined) {
                return action.payload.user.email
            }
            return null
        default:
            return prev
    }
}

const phone = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.phone !== undefined) {
                return action.payload.phone
            }
            return prev
        case Action.FETCH_SUCCESS:
            if (action.payload.user && action.payload.user.phone !== undefined) {
                return action.payload.user.phone
            }
            return null
        default:
            return prev
    }
}

const name = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.name !== undefined) {
                return action.payload.name
            }
            return prev
        case Action.FETCH_SUCCESS:
            if (action.payload.user && action.payload.user.name !== undefined) {
                return action.payload.user.name
            }
            return null
        default:
            return prev
    }
}

const avatar = (prev = null, action) => {
    switch (action.type) {
        case Action.UPLOAD_MEDIA_SUCCESS:
            return action.payload.id
        case Action.FETCH_SUCCESS:
            if (action.payload.user && action.payload.user.avatar !== undefined) {
                return action.payload.user.avatar
            }
            return null
        default:
            return prev
    }
}

const password = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.password !== undefined) {
                return action.payload.password
            }
            return prev
        default:
            return prev
    }
}

const password2 = (prev = null, action) => {
    switch (action.type) {
        case Action.MODEL_CHANGED:
            if (action.payload.password2 !== undefined) {
                return action.payload.password2
            }
            return prev
        default:
            return prev
    }
}

const isActive = (prev = true, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.user && action.payload.user.isActive !== undefined) {
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