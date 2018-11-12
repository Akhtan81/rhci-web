import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    isAdmin: store => store.User.model.isAdmin,
})
