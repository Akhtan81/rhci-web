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
        if (!model.id) {
            ++validator.count
        }
    }

    if (changes.category) {
        if (!model.category) {
            ++validator.count
            validator.errors.category = translator('validation_required')
        }
    } else {
        if (!model.id) {
            ++validator.count
        }
    }


    const isRequired = model.price !== null || model.unit !== null || model.minAmount !== null

    if (changes.price) {

        if (model.price === null) {
            if (isRequired) {
                ++validator.count
                validator.errors.price = translator('validation_required')
            }
        } else if (model.price < 0) {
            ++validator.count
            validator.errors.price = translator('validation_invalid')
        }
    } else {
        if (!model.id) {
            if (isRequired) {
                ++validator.count
            }
        }
    }

    if (changes.minAmount) {
        if (model.minAmount === null) {
            if (isRequired) {
                ++validator.count
                validator.errors.minAmount = translator('validation_required')
            }
        } else if (model.minAmount <= 0) {
            ++validator.count
            validator.errors.minAmount = translator('validation_invalid')
        }
    } else {
        if (!model.id) {
            if (isRequired) {
                ++validator.count
            }
        }
    }

    if (changes.unit) {
        if (model.unit === null) {
            if (isRequired) {
                ++validator.count
                validator.errors.unit = translator('validation_required')
            }
        } else if (!model.unit) {
            ++validator.count
            validator.errors.unit = translator('validation_required')
        }
    } else {
        if (!model.id) {
            if (isRequired) {
                ++validator.count
            }
        }
    }


    return validator
}