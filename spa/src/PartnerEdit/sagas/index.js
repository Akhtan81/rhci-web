import {all, fork} from 'redux-saga/effects'
import Validation from './Validation'

export default function* sagas() {
    yield all([
        fork(Validation),
    ])
}
