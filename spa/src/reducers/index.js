import {combineReducers} from 'redux'
import User from './User'
import UI from './UI'
import Login from '../Login/reducers'
import Category from '../Category/reducers'
import CategoryEdit from '../CategoryEdit/reducers'
import PartnerCategory from '../PartnerCategory/reducers'
import PartnerCategoryEdit from '../PartnerCategoryEdit/reducers'
import Partner from '../Partner/reducers'
import PartnerEdit from '../PartnerEdit/reducers'
import Order from '../Order/reducers'
import PartnerOrder from '../PartnerOrder/reducers'
import OrderEdit from '../OrderEdit/reducers'
import Profile from '../Profile/reducers'

export default combineReducers({
    UI,
    User,
    Login,
    Profile,
    Order,
    PartnerOrder,
    OrderEdit,
    Partner,
    PartnerEdit,
    Category,
    CategoryEdit,
    PartnerCategory,
    PartnerCategoryEdit,
})
