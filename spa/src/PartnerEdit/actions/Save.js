import request from 'axios'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

const parseBeforeSubmit = model => {
    const data = {...model}

    if (data.user.avatar && data.user.avatar.id) {
        data.user.avatar = data.user.avatar.id
    }

    if (data.country && data.country.id) {
        data.country = data.country.id
    }

    data.postalCodes = data.postalCodes.split(',')

    return data
}

export default (model, callback) => dispatch => {

    const data = parseBeforeSubmit(model)

    dispatch({
        type: SAVE_BEFORE
    })

    let promise
    if (data.id > 0) {
        promise = request.put(AppRouter.PUT.partner.replace('__ID__', data.id), data)
    } else {
        promise = request.post(AppRouter.POST.partner, data)
    }

    promise
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
