import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    PartnerCategoryEdit: store => store.PartnerCategoryEdit,
    PartnerCategory: store => store.PartnerCategory,
    Unit: store => store.Unit,
    Category: store => store.Category,
})
