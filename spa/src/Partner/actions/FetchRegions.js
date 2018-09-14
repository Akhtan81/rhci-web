import request from 'axios'
import {FETCH_REGIONS_BEFORE, FETCH_REGIONS_FAILURE, FETCH_REGIONS_SUCCESS} from '../actions'

export default (country) => dispatch => {

    const query = [
        'limit=0',
        'filter[country]=' + country
    ];

    dispatch({
        type: FETCH_REGIONS_BEFORE
    })

    request.get(AppRouter.GET.regions + (query ? '?' + query.join('&') : ""))
        .then(({data}) => {
            dispatch({
                type: FETCH_REGIONS_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) return

            dispatch({
                type: FETCH_REGIONS_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
