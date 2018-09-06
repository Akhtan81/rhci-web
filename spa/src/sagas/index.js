import {fork, all} from 'redux-saga/effects'
import Login from '../Login/sagas'
import Category from '../Category/sagas'
import Order from '../Order/sagas'
import District from '../District/sagas'
import Partner from '../Partner/sagas'

export default function* sagas() {
    yield all([
        fork(Login),
        fork(Order),
        fork(Category),
        fork(District),
        fork(Partner),
    ])
}
