import translator from '../../../translations/translator'

export default (model, changes) => {
    const validator = {
        count: 0,
        messages: [],
        errors: {}
    }

    if (changes.accountId) {
        if (!model.accountId) {
            ++validator.count
            validator.errors.accountId = translator('validation_required')
        }
    }

    return validator
}