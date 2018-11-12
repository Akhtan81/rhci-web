import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    CategoryEdit: store => store.CategoryEdit,
    Category: store => store.Category
})
