import request from '../../Common/request'
import {UPDATE_BEFORE, UPDATE_FAILURE, UPDATE_SUCCESS} from '../actions'

export default (model) => dispatch => {

    dispatch({
        type: UPDATE_BEFORE
    })

    request.put(AppRouter.PUT.payment.replace('__ID__', model.id), model)
        .then(({data}) => {
            dispatch({
                type: UPDATE_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) {
                console.log(e);
                return
            }

            dispatch({
                type: UPDATE_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
