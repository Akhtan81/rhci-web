import {fork, all} from 'redux-saga/effects'
import Login from './Login'

export default function* sagas() {
    yield all([
        fork(Login),
    ])
}
