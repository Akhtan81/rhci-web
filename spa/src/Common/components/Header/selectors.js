import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    isSidebarVisible: store => store.UI.isSidebarVisible,
    name: store => store.User.model.name,
    avatar: store => store.User.model.avatar,
    isAuthenticated: store => store.User.isAuthenticated
})
