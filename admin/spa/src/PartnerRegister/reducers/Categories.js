import {combineReducers} from 'redux'
import * as Action from '../actions'


const isLoading = (prev = false, action) => {
    switch (action.type) {
        case Action.FETCH_CATEGORIES_SUCCESS:
        case Action.FETCH_CATEGORIES_FAILURE:
            return false
        case Action.FETCH_CATEGORIES_BEFORE:
            return true
        default:
            return prev
    }
}

const items = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_CATEGORIES_SUCCESS:
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


export default combineReducers({
    isLoading,
    items,
})

