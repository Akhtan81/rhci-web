import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    OrderEdit: store => store.OrderEdit,
    isAdmin: store => store.User.model.isAdmin,
})
