import translator from '../../../translations/translator'

export default (model, changes) => {
    const validator = {
        count: 0,
        messages: [],
        errors: {}
    }

    // if (changes.name) {
    //     if (!model.name) {
    //         ++validator.count
    //         validator.errors.name = translator('validation_required')
    //     }
    // }


    return validator
}