import request from '../../Common/request'
import {DELETE_BEFORE, DELETE_FAILURE, DELETE_SUCCESS} from '../actions'

export default (model, callback) => dispatch => {

    dispatch({
        type: DELETE_BEFORE
    })

    request.delete(AppRouter.DELETE.category.replace('__ID__', model.id))
        .then(({data}) => {
            dispatch({
                type: DELETE_SUCCESS,
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
                type: DELETE_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
