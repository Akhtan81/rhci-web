import request from 'axios'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

export default (model) => dispatch => {

    dispatch({
        type: SAVE_BEFORE
    })

    request.put(AppRouter.PUT.order.replace('__ID__', model.id), model)
        .then(({data}) => {
            dispatch({
                type: SAVE_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) {
                console.log(e);
                return
            }

            dispatch({
                type: SAVE_FAILURE,
                payload: e.response.data
            })
        })
}
