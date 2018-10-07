import request from '../../Common/request'
import {FETCH_DISTRICTS_BEFORE, FETCH_DISTRICTS_FAILURE, FETCH_DISTRICTS_SUCCESS} from '../actions'

export default (city) => dispatch => {

    const query = [
        'limit=0',
        'filter[city]=' + city
    ];

    dispatch({
        type: FETCH_DISTRICTS_BEFORE
    })

    request.get(AppRouter.GET.districts + (query ? '?' + query.join('&') : ""))
        .then(({data}) => {
            dispatch({
                type: FETCH_DISTRICTS_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) return

            dispatch({
                type: FETCH_DISTRICTS_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
