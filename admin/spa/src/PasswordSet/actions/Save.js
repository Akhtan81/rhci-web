import request from '../../Common/request'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

export default (model, callback) => dispatch => {

    dispatch({
        type: SAVE_BEFORE
    })

    request.put(AppRouter.PUT.userPasswordSet.replace('__TOKEN__', model.token), model.user)
        .then(({data}) => {
            dispatch({
                type: SAVE_SUCCESS,
                payload: data
            })

            if (callback) {
                callback()
            }
        })
        .catch(e => {
            if (!e.response) {
                console.log(e);
                return
            }

            dispatch({
                type: SAVE_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
