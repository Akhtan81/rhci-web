import request from '../../Common/request'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

const parseBeforeSubmit = model => {
    const data = {...model}

    if (data.user) {
        if (data.user.avatar && data.user.avatar.id) {
            data.user.avatar = data.user.avatar.id
        }
    }

    if (data.country) {
        data.country = data.country.id
    }

    if (data.requestedCategories) {
        data.requestedCategories = data.requestedCategories.map(item => ({
            id: item.id,
            category: item.category.id
        }))
    }

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
