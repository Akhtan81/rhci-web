import {all, throttle} from 'redux-saga/effects'
import {SAVE_SUCCESS} from '../actions'

function* redirect() {
    window.location = AppRouter.GET.landing
}

export default function* sagas() {
    yield all([
        throttle(1500, SAVE_SUCCESS, redirect)
    ])
}
