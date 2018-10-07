import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    isAdmin: store => store.User.model.isAdmin,
    isPartner: store => store.User.model.partner && store.User.model.partner.id > 0,
    partner: store => store.User.model.partner,
})
