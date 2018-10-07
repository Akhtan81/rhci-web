import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    PartnerEdit: store => store.PartnerEdit,
    Country: store => store.Partner.Country,
})
