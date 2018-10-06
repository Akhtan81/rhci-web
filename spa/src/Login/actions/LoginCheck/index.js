import request from '../../../Common/request'
import {LOGIN_BEFORE, LOGIN_FAILURE, LOGIN_SUCCESS} from '../../actions'

export default (login, password, onSuccess) => dispatch => {

    dispatch({
        type: LOGIN_BEFORE
    })

    request.post(AppRouter.POST.login, {login, password})
        .then(({data}) => {
            dispatch({
                type: LOGIN_SUCCESS,
                payload: data
            })

            if (onSuccess)
                onSuccess()
        })
        .catch(e => {
            console.log(e);
            dispatch({
                type: LOGIN_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
