import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    isSidebarVisible: store => store.UI.isSidebarVisible,
    user: store => store.User.model,
    isAuthenticated: store => store.User.isAuthenticated,
    timezone: store => store.User.timezone
})
