import {all, fork} from 'redux-saga/effects'
import Validation from './Validation'
import Owners from './Owners'

export default function* sagas() {
    yield all([
        fork(Validation),
        fork(Owners),
    ])
}
