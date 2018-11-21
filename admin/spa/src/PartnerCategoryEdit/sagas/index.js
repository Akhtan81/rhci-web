import {all, fork} from 'redux-saga/effects'
import Validation from './Validation'
import Category from './Category'
import Redirect from './Redirect'

export default function* sagas() {
    yield all([
        fork(Validation),
        fork(Category),
        fork(Redirect),
    ])
}
