import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    ProfileUser: store => store.ProfileUser,
    isAdmin: store => store.User.model.isAdmin,
    user: store => store.User.model
})
