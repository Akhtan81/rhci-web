import {all, fork} from 'redux-saga/effects'
import Validation from './Validation'
import Country from './Country'

export default function* sagas() {
    yield all([
        fork(Validation),
        fork(Country),
    ])
}
