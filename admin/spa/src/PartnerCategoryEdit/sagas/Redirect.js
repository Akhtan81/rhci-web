import {all, throttle} from 'redux-saga/effects'
import {SAVE_SUCCESS} from '../actions'

function* redirect({payload}) {
    window.location = AppRouter.GET.categoryEdit.replace('__ID__', payload.id)
}

export default function* sagas() {
    yield all([
        throttle(500, SAVE_SUCCESS, redirect)
    ])
}
