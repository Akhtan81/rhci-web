import {all, fork} from 'redux-saga/effects'
import Validation from './Validation'
import Subscriptions from './Subscriptions'

export default function* sagas() {
    yield all([
        fork(Validation),
        fork(Subscriptions),
    ])
}
