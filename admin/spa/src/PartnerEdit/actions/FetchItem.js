import request from '../../Common/request'
import {FETCH_BEFORE, FETCH_FAILURE, FETCH_SUCCESS} from '../actions'

export default (id, errorCallback) => dispatch => {

    dispatch({
        type: FETCH_BEFORE
    })

    request.get(AppRouter.GET.partner.replace('__ID__', id))
        .then(({data}) => {
            dispatch({
                type: FETCH_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            console.log(e);
            if (!e.response) {
                console.log(e);
                return
            }

            dispatch({
                type: FETCH_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })

            if (errorCallback) {
                errorCallback()
            }
        })
}
