import request from '../../Common/request'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

const parseBeforeSubmit = model => {
    const data = {...model}

    if (data.name) {
        data.name = data.name.trim()
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
        promise = request.put(AppRouter.PUT.unit.replace('__ID__', data.id), data)
    } else {
        promise = request.post(AppRouter.POST.units, data)
    }

    promise
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
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
