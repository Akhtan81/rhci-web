import {all, fork} from 'redux-saga/effects'
import Login from './Login'
import Authentication from './Authentication'

export default function* sagas() {
    yield all([
        fork(Login),
        fork(Authentication),
    ])
}
