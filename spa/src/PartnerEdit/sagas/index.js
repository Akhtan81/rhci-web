import {all, fork} from 'redux-saga/effects'
import Validation from './Validation'
import Geo from './Geo'

export default function* sagas() {
    yield all([
        fork(Validation),
        fork(Geo),
    ])
}
