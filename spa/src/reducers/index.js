import {combineReducers} from 'redux'
import Login from '../Login/reducers'
import Category from '../Category/reducers'
import CategoryEdit from '../CategoryEdit/reducers'
import PartnerCategory from '../PartnerCategory/reducers'
import PartnerCategoryEdit from '../PartnerCategoryEdit/reducers'
import Partner from '../Partner/reducers'
import PartnerEdit from '../PartnerEdit/reducers'
import Order from '../Order/reducers'
import OrderEdit from '../OrderEdit/reducers'
import User from './User'
import UI from './UI'

export default combineReducers({
    UI,
    User,
    Login,
    Order,
    OrderEdit,
    Partner,
    PartnerEdit,
    Category,
    CategoryEdit,
    PartnerCategory,
    PartnerCategoryEdit,
})
