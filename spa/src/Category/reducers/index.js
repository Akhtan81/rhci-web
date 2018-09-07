import {combineReducers} from 'redux'
import * as Action from '../actions'

const initialFilter = {
    type: 'junk_removal',
    locale: AppParameters.locale
}
const filter = (prev = initialFilter, action) => {
    switch (action.type) {
        case Action.FILTER_CHANGED:
            return {
                ...prev,
                ...action.payload
            }
        default:
            return prev
    }
}

const items = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
            const tree = action.payload.items

            const items = [];

            const addChild = item => {

                items.push(item)

                if (item.children) {
                    item.children.forEach(addChild)
                }
            }

            tree.forEach(addChild)

            return items;
        default:
            return prev
    }
}

const isLoading = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:
        case Action.FETCH_FAILURE:
            return false
        case Action.FETCH_BEFORE:
            return true
        default:
            return prev
    }
}

export default combineReducers({
    filter,
    items,
    isLoading,
})
