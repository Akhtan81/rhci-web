import request from '../../Common/request'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

export default (model, successCallback) => dispatch => {

    dispatch({
        type: SAVE_BEFORE
    })

    request.put(AppRouter.PUT.order.replace('__ID__', model.id), model)
        .then(({data}) => {
            dispatch({
                type: SAVE_SUCCESS,
                payload: data
            })

            if (successCallback) {
                successCallback(data)
            }
        })
        .catch(e => {
            if (!e.response) return

            dispatch({
                type: SAVE_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
