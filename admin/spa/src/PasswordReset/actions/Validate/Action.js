import translator from '../../../translations/translator'

export default (model, changes) => {
    const validator = {
        count: 0,
        messages: [],
        errors: {}
    }

    if (changes.login) {
        if (!model.login) {
            ++validator.count
            validator.errors.password = translator('validation_required')
        } else if (model.login.length < 3) {
            ++validator.count
            validator.errors.password = translator('validation_too_short')
        }
    } else {
        ++validator.count
    }


    return validator
}