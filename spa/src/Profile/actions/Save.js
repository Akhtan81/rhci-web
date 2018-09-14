import request from '../../Common/request'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

const parseBeforeSubmit = model => {
    const data = {...model}

    return data
}

export default (model, callback) => dispatch => {

    const data = parseBeforeSubmit(model)

    dispatch({
        type: SAVE_BEFORE
    })

    request.put(AppRouter.PUT.partnerMe, data)
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
