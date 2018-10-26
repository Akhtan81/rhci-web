import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    Order: store => store.PartnerOrder,
    User: store => store.User.model,
})
