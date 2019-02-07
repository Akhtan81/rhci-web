import {all, fork} from 'redux-saga/effects'
import Validation from './Validation'
import Unit from './Unit'

export default function* sagas() {
    yield all([
        fork(Unit),
        fork(Validation),
    ])
}
