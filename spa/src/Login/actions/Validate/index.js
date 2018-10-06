import validate from './Action'
import {LOGIN_VALIDATE_FAILURE, LOGIN_VALIDATE_SUCCESS} from '../../actions'

export default (model, changes) => dispatch => {
    const validator = validate(model, changes)

    if (validator.total === 0) {
        dispatch({
            type: LOGIN_VALIDATE_SUCCESS
        })
    } else {
        dispatch({
            type: LOGIN_VALIDATE_FAILURE,
            payload: validator
        })
    }

    return validator
}