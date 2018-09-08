import {combineReducers} from 'redux'
import * as Action from '../actions'
import user from './user'


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

const createdAt = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.createdAt !== undefined) {
                return action.payload.createdAt
            }
            return null
        default:
            return prev
    }
}

const district = (prev = null, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            if (action.payload.district !== undefined) {
                return action.payload.district.id
            }
            return null
        default:
            return prev
    }
}

export default combineReducers({
    id,
    createdAt,
    user,
    district,
})