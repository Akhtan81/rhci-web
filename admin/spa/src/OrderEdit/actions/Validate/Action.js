import translator from '../../../translations/translator'
import moment from 'moment'

export default (model, changes) => {
    const validator = {
        count: 0,
        messages: [],
        errors: {}
    }

    if (changes.scheduledAt) {
        if (!model.scheduledAt) {
            ++validator.count
            validator.errors.scheduledAt = translator('validation_required')
        } else if (model.createdAt) {
            const date1 = moment(model.createdAt, 'YYYY-MM-DD HH:mm:ss')
            const date2 = moment(model.scheduledAt, 'YYYY-MM-DD HH:mm')

            if (date2.isBefore(date1)) {
                ++validator.count
                validator.errors.scheduledAt = translator('validation_invalid')
            }
        }
    }

    return validator
}