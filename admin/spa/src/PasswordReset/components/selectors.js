import {createStructuredSelector} from 'reselect'

export default createStructuredSelector({
    PasswordReset: store => store.PasswordReset,
})
