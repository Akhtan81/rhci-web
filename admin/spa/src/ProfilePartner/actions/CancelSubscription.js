import request from '../../Common/request'
import {CANCEL_SUBSCRIPTION_BEFORE, CANCEL_SUBSCRIPTION_FAILURE, CANCEL_SUBSCRIPTION_SUCCESS} from '../actions'

export default () => dispatch => {

    dispatch({
        type: CANCEL_SUBSCRIPTION_BEFORE
    })

    request.post(AppRouter.POST.partnerSubscriptionsCancel)
        .then(({data}) => {
            dispatch({
                type: CANCEL_SUBSCRIPTION_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) {
                console.log(e);
                return
            }

            dispatch({
                type: CANCEL_SUBSCRIPTION_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
