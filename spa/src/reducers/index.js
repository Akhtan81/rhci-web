import {combineReducers} from 'redux'
import Login from '../Login/reducers'
import Category from '../Category/reducers'
import CategoryEdit from '../CategoryEdit/reducers'
import PartnerCategory from '../PartnerCategory/reducers'
import PartnerCategoryEdit from '../PartnerCategoryEdit/reducers'
import Partner from '../Partner/reducers'
import District from '../District/reducers'
import Order from '../Order/reducers'
import User from './User'
import UI from './UI'

export default combineReducers({
    Order,
    District,
    Partner,
    Login,
    Category,
    CategoryEdit,
    PartnerCategory,
    PartnerCategoryEdit,
    User,
    UI,
})
