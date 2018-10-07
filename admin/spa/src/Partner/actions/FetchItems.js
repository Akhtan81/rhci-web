import request from '../../Common/request'
import {FETCH_BEFORE, FETCH_FAILURE, FETCH_SUCCESS} from '../actions'

export default (filter, page) => dispatch => {

    const query = [
        'page=' + page
    ];

    if (filter) {
        if (filter.search) {
            query.push('filter[search]=' + filter.search)
        }
        if (filter.statuses) {
            query.push('filter[statuses]=' + filter.statuses)
        }
        if (filter.district > 0) {
            query.push('filter[district]=' + filter.district)
        }
        if (filter.city > 0) {
            query.push('filter[city]=' + filter.city)
        }
        if (filter.region > 0) {
            query.push('filter[region]=' + filter.region)
        }
        if (filter.country > 0) {
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
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
