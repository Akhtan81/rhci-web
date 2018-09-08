import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    PartnerEdit: store => store.PartnerEdit,
    Country: store => store.Partner.Country,
    Region: store => store.Partner.Region,
    District: store => store.Partner.District,
    City: store => store.Partner.City,
})
