import validate from './Action'
import {VALIDATE_FAILURE, VALIDATE_SUCCESS} from '../../actions'

export default (model, changes) => dispatch => {
    const validator = validate(model, changes)

    if (validator.count === 0) {
        dispatch({
            type: VALIDATE_SUCCESS
        })
    } else {
        dispatch({
            type: VALIDATE_FAILURE,
            payload: validator
        })
    }

    return validator
}