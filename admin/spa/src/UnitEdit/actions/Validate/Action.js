import translator from '../../../translations/translator'
import {objectValues} from "../../../Common/utils";

export default (model, changes) => {
    const validator = {
        count: 0,
        messages: [],
        errors: {}
    }

    if (changes.translation) {

        validator.errors.trans = {}

        objectValues(model.translations).forEach(trans => {

            validator.errors.trans[trans.locale] = {}

            if (!trans.name) {
                ++validator.count
                validator.errors.trans[trans.locale].name = translator('validation_required')
            }
        })

    } else {
        if (!model.id) {
            ++validator.count
        }
    }

    return validator
}