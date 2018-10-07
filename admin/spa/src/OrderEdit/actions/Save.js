import request from '../../Common/request'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

const parseBeforeSubmit = model => {
    const data = {...model}

    if (data.price !== null) {
        let newPrice = Math.ceil(data.price * 100);
        if (isNaN(newPrice)) newPrice = 0;

        data.price = newPrice
    }

    return data
}

export default (model, successCallback) => dispatch => {

    const data = parseBeforeSubmit(model)

    dispatch({
        type: SAVE_BEFORE
    })

    request.put(AppRouter.PUT.order.replace('__ID__', data.id), data)
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
