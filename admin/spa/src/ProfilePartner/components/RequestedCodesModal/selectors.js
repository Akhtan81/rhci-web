import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    isLoading: store => store.ProfilePartner.isLoading,
    id: store => store.ProfilePartner.model.id,
    RequestedCodes: store => store.ProfilePartner.RequestedCodes,
    OrderTypes: store => store.ProfilePartner.OrderTypes,
})
