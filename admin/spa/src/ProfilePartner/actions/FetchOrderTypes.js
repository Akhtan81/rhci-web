import request from '../../Common/request'
import {FETCH_ORDER_TYPES_BEFORE, FETCH_ORDER_TYPES_FAILURE, FETCH_ORDER_TYPES_SUCCESS} from '../actions'

export default () => dispatch => {

    dispatch({
        type: FETCH_ORDER_TYPES_BEFORE
    })

    request.get(AppRouter.GET.orderTypes)
        .then(({data}) => {
            dispatch({
                type: FETCH_ORDER_TYPES_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            console.log(e);
            if (!e.response) {
                console.log(e);
                return
            }

            dispatch({
                type: FETCH_ORDER_TYPES_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
