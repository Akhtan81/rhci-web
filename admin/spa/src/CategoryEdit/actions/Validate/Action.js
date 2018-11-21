import translator from '../../../translations/translator'

export default (model, changes) => {
    const validator = {
        count: 0,
        messages: [],
        errors: {}
    }

    if (changes.name) {
        if (!model.name) {
            ++validator.count
            validator.errors.name = translator('validation_required')
        }
    }

    // if (changes.locale) {
    //     if (!model.locale) {
    //         ++validator.count
    //         validator.errors.locale = translator('validation_required')
    //     }
    // }

    if (changes.type) {
        if (!model.type) {
            ++validator.count
            validator.errors.type = translator('validation_required')
        }
    }

    return validator
}