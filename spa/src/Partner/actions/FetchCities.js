import request from 'axios'
import {FETCH_CITIES_BEFORE, FETCH_CITIES_FAILURE, FETCH_CITIES_SUCCESS} from '../actions'

export default (region) => dispatch => {

    const query = [
        'limit=0',
        'filter[region]=' + region
    ];

    dispatch({
        type: FETCH_CITIES_BEFORE
    })

    request.get(AppRouter.GET.cities + (query ? '?' + query.join('&') : ""))
        .then(({data}) => {
            dispatch({
                type: FETCH_CITIES_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) return

            dispatch({
                type: FETCH_CITIES_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
