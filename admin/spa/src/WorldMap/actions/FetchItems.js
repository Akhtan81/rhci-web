import request from '../../Common/request'
import {FETCH_BEFORE, FETCH_FAILURE, FETCH_SUCCESS} from '../actions'

export default (filter) => dispatch => {

    dispatch({
        type: FETCH_BEFORE
    })

    const query = [
        'limit=0'
    ]

    Object.keys(filter).forEach(key => {
        query.push('filter[' + key + ']=' + filter[key])
    })

    request.get(AppRouter.GET.orderLocations + '?' + query.join('&'))
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
