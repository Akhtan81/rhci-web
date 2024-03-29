import request from '../../Common/request'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'
import {objectValues} from "../../Common/utils";

const parseBeforeSubmit = model => {
    const data = {...model}

    data.requestedPostalCodes = objectValues(data.requestedPostalCodes)

    data.requestedCategories = objectValues(data.requestedCategories).filter(item => !!item.category)

    return data
}

export default (model, callback) => dispatch => {

    const data = parseBeforeSubmit(model)

    dispatch({
        type: SAVE_BEFORE
    })

    request.post(AppRouter.POST.partnerSignup, data)
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
