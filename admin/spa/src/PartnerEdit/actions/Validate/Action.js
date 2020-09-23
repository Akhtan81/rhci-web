import translator from '../../../translations/translator'
import EmailValidator from 'email-validator'
import PasswordValidator from 'password-validator'

const passwordSchema = new PasswordValidator();

passwordSchema
    .is().min(8)
    .is().max(100)
// .has().uppercase()
// .has().lowercase()
// .has().digits()
    .has().not().spaces()

export default (model, changes) => {
    const validator = {
        count: 0,
        messages: [],
        errors: {}
    }

    if (changes.name) {
        if (!model.user.name) {
            ++validator.count
            validator.errors.name = translator('validation_required')
        }
    } else {
        if (!model.id) {
            ++validator.count
        }
    }

    // if (changes.country) {
    //     if (!model.country) {
    //         ++validator.count
    //         validator.errors.country = translator('validation_required')
    //     }
    // } else {
    //     if (!model.id) {
    //         ++validator.count
    //     }
    // }

    const postalCodeValidator =

        validator.errors.postalCodes = {}

    if (model.postalCodesJunkRemoval) {

        const codes = model.postalCodesJunkRemoval.split(',')

        codes.forEach(item => {
            if (!item) {
                ++validator.count
                validator.errors.postalCodesJunkRemoval = translator('validation_required')
            }
        })
    }

    if (model.postalCodesRecycling) {

        const codes = model.postalCodesRecycling.split(',')

        codes.forEach(item => {
            if (!item) {
                ++validator.count
                validator.errors.postalCodesRecycling = translator('validation_required')
            }
        })
    }

    if (model.postalCodesShredding) {

        const codes = model.postalCodesShredding.split(',')

        codes.forEach(item => {
            if (!item) {
                ++validator.count
                validator.errors.postalCodesShredding = translator('validation_required')
            }
        })
    }

    if (model.postalCodesBusyBee) {

        const codes = model.postalCodesBusyBee.split(',')

        codes.forEach(item => {
            if (!item) {
                ++validator.count
                validator.errors.postalCodesBusyBee = translator('validation_required')
            }
        })
    }

    if (model.postalCodesMoving) {

        const codes = model.postalCodesMoving.split(',')

        codes.forEach(item => {
            if (!item) {
                ++validator.count
                validator.errors.postalCodesMoving = translator('validation_required')
            }
        })
    }

    if (model.postalCodesDonation) {

        const codes = model.postalCodesDonation.split(',')

        codes.forEach(item => {
            if (!item) {
                ++validator.count
                validator.errors.postalCodesDonation = translator('validation_required')
            }
        })
    }

    if (changes.email) {
        if (!model.user.email) {
            ++validator.count
            validator.errors.email = translator('validation_required')
        } else if (!EmailValidator.validate(model.user.email)) {
            ++validator.count
            validator.errors.email = translator('validation_invalid')
        }
    }

    if (changes.phone) {
        if (model.user.phone) {
            if (model.user.phone.length < 7) {
                ++validator.count
                validator.errors.phone = translator('validation_invalid')
            }
        }
    }

    if (changes.password) {
        if (!model.user.password) {
            ++validator.count
            validator.errors.password = translator('validation_required')
        } else if (!passwordSchema.validate(model.user.password)) {
            ++validator.count
            validator.errors.password = translator('validation_invalid')
        }
    } else {
        if (!model.id) {
            ++validator.count
        }
    }

    if (changes.password2) {
        if (model.user.password2) {
            if (!passwordSchema.validate(model.user.password2)) {
                ++validator.count
                validator.errors.password2 = translator('validation_invalid')
            } else if (model.user.password2 !== model.user.password) {
                ++validator.count
                validator.errors.password2 = translator('validation_password_mismatch')
            }
        }
    } else {
        if (!model.id) {
            ++validator.count
        }
    }

    if (changes.address) {
        if (!model.location.address) {
            ++validator.count
            validator.errors.address = translator('validation_required')
        }
    } else {
        if (!model.id) {
            ++validator.count
        }
    }

    if (!model.user.email && !model.user.phone) {
        ++validator.count
    }

    return validator
}
