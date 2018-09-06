import {combineReducers} from 'redux'
import {TOGGLE_SIDEBAR} from "../Common/actions";

const isSidebarVisible = (state = false, action) => {
    switch (action.type) {
        case TOGGLE_SIDEBAR:
            return action.payload.isSidebarVisible
        default:
            return state
    }
}

export default combineReducers({
    isSidebarVisible,
})
