import request from '../../Common/request'
import {FETCH_BEFORE, FETCH_FAILURE, FETCH_SUCCESS} from '../actions'

export default (errorCallback) => dispatch => {

    dispatch({
        type: FETCH_BEFORE
    })

    request.get(AppRouter.GET.partnerMe)
        .then(({data}) => {
            dispatch({
                type: FETCH_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
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
