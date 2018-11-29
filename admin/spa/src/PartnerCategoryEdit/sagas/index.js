import {all, fork} from 'redux-saga/effects'
import Validation from './Validation'
import Category from './Category'

export default function* sagas() {
    yield all([
        fork(Validation),
        fork(Category),
    ])
}
