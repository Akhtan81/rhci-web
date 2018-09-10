import {all, fork} from 'redux-saga/effects'
import Validation from './Validation'
import Order from './Order'

export default function* sagas() {
    yield all([
        fork(Validation),
        fork(Order),
    ])
}
