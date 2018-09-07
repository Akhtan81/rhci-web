import request from 'axios'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

export default model => dispatch => {

    dispatch({
        type: SAVE_BEFORE
    })

    let promise
    if (model.id > 0) {
        promise = request.put(AppRouter.PUT.category.replace('__ID__', model.id), model)
    } else {
        promise = request.post(AppRouter.POST.category, model)
    }

    promise
        .then(({data}) => {
            dispatch({
                type: SAVE_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) return

            dispatch({
                type: SAVE_FAILURE,
                payload: e.response.data
            })
        })
}
