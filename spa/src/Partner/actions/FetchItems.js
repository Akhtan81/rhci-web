import request from 'axios'
import {FETCH_BEFORE, FETCH_FAILURE, FETCH_SUCCESS} from '../actions'

export default (filter, page) => dispatch => {

    const query = [
        'page=' + page
    ];

    if (filter) {
        if (filter.search !== undefined) {
            query.push('filter[search]=' + filter.search)
        }
        if (filter.district !== undefined) {
            query.push('filter[district]=' + filter.district)
        }
        if (filter.city !== undefined) {
            query.push('filter[city]=' + filter.city)
        }
        if (filter.region !== undefined) {
            query.push('filter[region]=' + filter.region)
        }
        if (filter.country !== undefined) {
            query.push('filter[country]=' + filter.country)
        }
        if (filter.isActive !== undefined) {
            query.push('filter[isActive]=' + filter.isActive)
        }
    }

    dispatch({
        type: FETCH_BEFORE
    })

    request.get(AppRouter.GET.partners + (query ? '?' + query.join('&') : ""))
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
                payload: e.response.data
            })
        })
}
