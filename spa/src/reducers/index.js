import {combineReducers} from 'redux'
import Login from '../Login/reducers'
import Category from '../Category/reducers'
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
    User,
    UI,
})
