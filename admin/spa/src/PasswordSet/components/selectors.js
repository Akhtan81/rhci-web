import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    PasswordSet: store => store.PasswordSet,
})
