import {combineReducers} from 'redux'
import * as Action from '../actions'
import keyBy from "lodash/keyBy";
import {cid} from "../../Common/utils";

const isModalOpen = (prev = false, action) => {
    switch (action.type) {
        case Action.SAVE_SUCCESS:
            return false
        case Action.TOGGLE_REQUESTED_CODES_MODAL:
            return !prev
        default:
            return prev
    }
}

const initialRequest = keyBy([
    {
        cid: cid(),
        postalCode: null,
        type: null,
    }
], 'cid')

const requestedPostalCodes = (prev = initialRequest, action) => {
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
        case Action.REMOVE_POSTAL_CODE:
            state = {...prev}

            delete state[action.payload.cid]

            return state
        case Action.ADD_POSTAL_CODE:

            const item = {
                cid: cid(),
                postalCode: null,
                type: null,
            }

            return {
                ...prev,
                [item.cid]: item
            }
        case Action.SAVE_SUCCESS:
            return {...initialRequest}
        default:
            return prev
    }
}


export default combineReducers({
    isModalOpen,
    requestedPostalCodes,
})

