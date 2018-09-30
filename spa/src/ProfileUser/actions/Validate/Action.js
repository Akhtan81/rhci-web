import translator from '../../../translations/translator'
import EmailValidator from "email-validator";
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

    if (changes.email) {
        if (!model.email) {
            ++validator.count
            validator.errors.email = translator('validation_required')
        } else if (!EmailValidator.validate(model.email)) {
            ++validator.count
            validator.errors.email = translator('validation_invalid')
        }
    }

    if (changes.phone) {
        if (model.phone) {
            if (model.phone.length < 7) {
                ++validator.count
                validator.errors.phone = translator('validation_invalid')
            }
        }
    }

    if (changes.password) {
        if (!model.password) {
            ++validator.count
            validator.errors.password = translator('validation_required')
        } else if (!passwordSchema.validate(model.password)) {
            ++validator.count
            validator.errors.password = translator('validation_invalid')
        }
    } else {
        if (!model.id) {
            ++validator.count
        }
    }

    if (changes.password2) {
        if (model.password2) {
            if (!passwordSchema.validate(model.password2)) {
                ++validator.count
                validator.errors.password2 = translator('validation_invalid')
            } else if (model.password2 !== model.password) {
                ++validator.count
                validator.errors.password2 = translator('validation_password_mismatch')
            }
        }
    } else {
        if (!model.id) {
            ++validator.count
        }
    }

    if (!model.email && !model.phone) {
        ++validator.count
    }

    return validator
}