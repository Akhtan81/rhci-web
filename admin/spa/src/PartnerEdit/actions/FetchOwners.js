import request from '../../Common/request'
import {FETCH_OWNERS_BEFORE, FETCH_OWNERS_FAILURE, FETCH_OWNERS_SUCCESS} from '../actions'

export default content => dispatch => {

    dispatch({
        type: FETCH_OWNERS_BEFORE
    })

    request.post(AppRouter.POST.postalCodeOwners, {
        'postalCodes': content
    })
        .then(({data}) => {
            dispatch({
                type: FETCH_OWNERS_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            console.log(e);
            if (!e.response) return

            dispatch({
                type: FETCH_OWNERS_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
