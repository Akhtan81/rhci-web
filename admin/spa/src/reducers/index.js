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
import PartnerRegister from '../PartnerRegister/reducers'
import Order from '../Order/reducers'
import PartnerOrder from '../PartnerOrder/reducers'
import OrderEdit from '../OrderEdit/reducers'
import ProfileUser from '../ProfileUser/reducers'
import ProfilePartner from '../ProfilePartner/reducers'
import PasswordReset from '../PasswordReset/reducers'
import PasswordSet from '../PasswordSet/reducers'
import WorldMap from '../WorldMap/reducers'
import Unit from '../Unit/reducers'
import UnitEdit from '../UnitEdit/reducers'

export default combineReducers({
    UI,
    User,
    Login,
    ProfileUser,
    ProfilePartner,
    Order,
    PartnerOrder,
    OrderEdit,
    Partner,
    PartnerEdit,
    PartnerRegister,
    Category,
    CategoryEdit,
    PartnerCategory,
    PartnerCategoryEdit,
    PasswordReset,
    PasswordSet,
    WorldMap,
    Unit,
    UnitEdit,
})
