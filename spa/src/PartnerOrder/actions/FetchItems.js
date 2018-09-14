import request from '../../Common/request'
import {FETCH_BEFORE, FETCH_FAILURE, FETCH_SUCCESS} from '../actions'

export default (filter, page = 1) => dispatch => {

    const query = [
        'page=' + page
    ];

    if (filter) {
        if (filter.search) {
            query.push('filter[search]=' + filter.search)
        }
        if (filter.status) {
            query.push('filter[status]=' + filter.status)
        }
    }

    dispatch({
        type: FETCH_BEFORE
    })

    request.get(AppRouter.GET.orders + (query ? '?' + query.join('&') : ""))
        .then(({data}) => {
            dispatch({
                type: FETCH_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) return

            dispatch({
                type: FETCH_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
