import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    isSidebarVisible: store => store.UI.isSidebarVisible,
    isAdmin: store => store.User.model.isAdmin,
    user: store => store.User.model,
})
