import {fork, all} from 'redux-saga/effects'
import Login from '../Login/sagas'
import Category from '../Category/sagas'
import CategoryEdit from '../CategoryEdit/sagas'
import PartnerCategory from '../PartnerCategory/sagas'
import PartnerCategoryEdit from '../PartnerCategoryEdit/sagas'
import Order from '../Order/sagas'
import PartnerOrder from '../Order/sagas'
import OrderEdit from '../OrderEdit/sagas'
import Partner from '../Partner/sagas'
import PartnerEdit from '../PartnerEdit/sagas'
import Profile from '../Profile/sagas'
import PartnerRegister from '../PartnerRegister/sagas'
import PasswordReset from '../PasswordReset/sagas'
import PasswordSet from '../PasswordSet/sagas'

export default function* sagas() {
    yield all([
        fork(Login),
        fork(Order),
        fork(PartnerOrder),
        fork(OrderEdit),
        fork(Category),
        fork(CategoryEdit),
        fork(PartnerCategory),
        fork(PartnerCategoryEdit),
        fork(Partner),
        fork(PartnerEdit),
        fork(Profile),
        fork(PartnerRegister),
        fork(PasswordReset),
        fork(PasswordSet),
    ])
}
