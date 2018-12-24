import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    id: store => store.ProfilePartner.model.id,
    RequestedCodes: store => store.ProfilePartner.RequestedCodes,
})
