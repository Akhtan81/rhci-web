import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    isSidebarVisible: store => store.UI.isSidebarVisible
})
