import request from 'axios'
import {FETCH_COUNTRIES_BEFORE, FETCH_COUNTRIES_FAILURE, FETCH_COUNTRIES_SUCCESS} from '../actions'

export default () => dispatch => {

    const query = [
        'limit=0'
    ];

    dispatch({
        type: FETCH_COUNTRIES_BEFORE
    })

    request.get(AppRouter.GET.countries + (query ? '?' + query.join('&') : ""))
        .then(({data}) => {
            dispatch({
                type: FETCH_COUNTRIES_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) return

            dispatch({
                type: FETCH_COUNTRIES_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
