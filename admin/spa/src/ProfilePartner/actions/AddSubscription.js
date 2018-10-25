import request from '../../Common/request'
import {UPDATE_SUBSCRIPTION_BEFORE, UPDATE_SUBSCRIPTION_FAILURE, UPDATE_SUBSCRIPTION_SUCCESS} from '../actions'

export default () => dispatch => {

    dispatch({
        type: UPDATE_SUBSCRIPTION_BEFORE
    })

    request.post(AppRouter.POST.partnerSubscriptions)
        .then(({data}) => {
            dispatch({
                type: UPDATE_SUBSCRIPTION_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) return

            dispatch({
                type: UPDATE_SUBSCRIPTION_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
