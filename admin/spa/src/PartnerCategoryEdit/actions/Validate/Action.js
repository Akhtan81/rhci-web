import translator from '../../../translations/translator'

export default (model, changes) => {
    const validator = {
        count: 0,
        messages: [],
        errors: {}
    }

    if (changes.type) {
        if (!model.type) {
            ++validator.count
            validator.errors.type = translator('validation_required')
        }
    } else {
        ++validator.count
    }

    if (changes.category) {
        if (!model.category) {
            ++validator.count
            validator.errors.category = translator('validation_required')
        }
    } else {
        ++validator.count
    }

    if (changes.price) {
        if (model.price === null || model.price <= 0) {
            ++validator.count
            validator.errors.price = translator('validation_invalid')
        }
    }

    if (model.category && model.category.isSelectable) {

        if (changes.minAmount) {
            if (model.minAmount === null || model.minAmount <= 0) {
                ++validator.count
                validator.errors.minAmount = translator('validation_invalid')
            }
        } else {
            ++validator.count
        }

        if (changes.unit) {
            if (!model.unit) {
                ++validator.count
                validator.errors.unit = translator('validation_required')
            }
        } else {
            ++validator.count
        }

    } else {

        if (changes.minAmount) {
            if (model.minAmount <= 0) {
                ++validator.count
                validator.errors.minAmount = translator('validation_invalid')
            }
        }

        if (changes.unit) {
            if (!model.unit) {
                ++validator.count
                validator.errors.unit = translator('validation_required')
            }
        }
    }

    return validator
}