import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    isLoading: store => store.ProfilePartner.isLoading,
    id: store => store.ProfilePartner.model.id,
    RequestedCategories: store => store.ProfilePartner.RequestedCategories,
    Categories: store => store.ProfilePartner.Categories,
})
