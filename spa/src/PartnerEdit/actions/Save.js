import request from 'axios'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

export default model => dispatch => {

    const data = {...model}

    dispatch({
        type: SAVE_BEFORE
    })

    if (data.district && data.district.id) {
        data.district = data.district.id
    }

    delete data.city
    delete data.region
    delete data.country
    delete data.originalDistrict

    console.log(data);

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
