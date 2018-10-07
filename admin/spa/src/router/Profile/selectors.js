import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    isPartner: store => store.User.model.partner && store.User.model.partner.id,
})
