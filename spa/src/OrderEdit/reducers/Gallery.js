import {combineReducers} from 'redux'
import * as Action from '../actions'

const isOpen = (prev = false, action) => {
    switch (action.type) {
        case Action.TOGGLE_GALLERY:
            return !prev
        case Action.FETCH_BEFORE:
            return false
        default:
            return prev
    }
}

const images = (prev = [], action) => {
    switch (action.type) {
        case Action.FETCH_SUCCESS:

            const src = []

            if (action.payload.message) {
                if (action.payload.message.media) {
                    action.payload.message.media.forEach(item => {
                        src.push({
                            src: 'http://0.0.0.0:12020/img/favicon/apple-touch-icon-72x72.png'
                        })
                    })
                }
            }

            action.payload.items.forEach(item => {
                if (item.message && item.message.media) {
                    item.message.media.forEach(item => {
                        src.push({
                            src: 'http://0.0.0.0:12020/img/favicon/apple-touch-icon-128x128.png'
                        })
                    })
                }
            })

            return src
        case Action.FETCH_BEFORE:
            return []
        default:
            return prev
    }
}

const currentImage = (prev = 0, action) => {
    switch (action.type) {
        case Action.FETCH_BEFORE:
            return 0
        case Action.SET_GALLERY_IMAGE:
        case Action.TOGGLE_GALLERY:
            return action.payload || 0
        default:
            return prev
    }
}

export default combineReducers({
    currentImage,
    images,
    isOpen,
})
