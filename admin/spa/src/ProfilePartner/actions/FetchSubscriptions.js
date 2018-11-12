import request from '../../Common/request'
import {FETCH_SUBSCRIPTIONS_BEFORE, FETCH_SUBSCRIPTIONS_FAILURE, FETCH_SUBSCRIPTIONS_SUCCESS} from '../actions'

export default () => dispatch => {

    dispatch({
        type: FETCH_SUBSCRIPTIONS_BEFORE
    })

    request.get(AppRouter.GET.partnerSubscriptions)
        .then(({data}) => {
            dispatch({
                type: FETCH_SUBSCRIPTIONS_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) return

            dispatch({
                type: FETCH_SUBSCRIPTIONS_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
