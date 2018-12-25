import {combineReducers} from 'redux'
import * as Action from '../actions'
import keyBy from "lodash/keyBy";
import {cid} from "../../Common/utils";

const isModalOpen = (prev = false, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
            return false
        case Action.TOGGLE_REQUESTED_CATEGORIES_MODAL:
            return !prev
        default:
            return prev
    }
}

const initialRequest = keyBy([
    {
        cid: cid(),
        category: null,
    }
], 'cid')

const requestedCategories = (prev = initialRequest, action) => {
    let state
    switch (action.type) {
        case Action.MODEL_CHANGED:

            if (action.payload.request === undefined) {
                return prev
            }

            const id = action.payload.request.cid

            if (id === undefined) {
                return prev
            }

            state = {...prev}

            return {
                ...prev,
                [id]: {
                    ...prev[id],
                    ...action.payload.request
                }
            }
        case Action.REMOVE_CATEGORY:
            state = {...prev}

            delete state[action.payload.cid]

            return state
        case Action.ADD_CATEGORY:
            return {
                ...prev,
                [action.payload.cid]: action.payload
            }
        case Action.SAVE_SUCCESS:
            return {...initialRequest}
        default:
            return prev
    }
}


export default combineReducers({
    isModalOpen,
    requestedCategories,
})

