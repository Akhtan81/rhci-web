import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    Subscriptions: store => store.ProfilePartner.Subscriptions,
})
