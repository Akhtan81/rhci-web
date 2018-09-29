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

export default model => dispatch => {

    const data = parseBeforeSubmit(model)

    dispatch({
        type: SAVE_BEFORE
    })

    let promise
    if (data.id > 0) {
        promise = request.put(AppRouter.PUT.category.replace('__ID__', data.id), data)
    } else {
        promise = request.post(AppRouter.POST.category, data)
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
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
