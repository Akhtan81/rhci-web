import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    PartnerCategoryEdit: store => store.PartnerCategoryEdit,
    PartnerCategory: store => store.PartnerCategory
})
