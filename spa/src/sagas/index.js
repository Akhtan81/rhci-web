import {fork, all} from 'redux-saga/effects'
import Login from '../Login/sagas'
import Category from '../Category/sagas'
import CategoryEdit from '../CategoryEdit/sagas'
import PartnerCategory from '../PartnerCategory/sagas'
import PartnerCategoryEdit from '../PartnerCategoryEdit/sagas'
import Order from '../Order/sagas'
import Partner from '../Partner/sagas'
import PartnerEdit from '../PartnerEdit/sagas'

export default function* sagas() {
    yield all([
        fork(Login),
        fork(Order),
        fork(Category),
        fork(CategoryEdit),
        fork(PartnerCategory),
        fork(PartnerCategoryEdit),
        fork(Partner),
        fork(PartnerEdit),
    ])
}