import request from '../../Common/request'
import {FETCH_BEFORE, FETCH_FAILURE, FETCH_SUCCESS} from '../actions'

export default filter => dispatch => {

    const query = [];

    if (filter) {
        if (filter.type) {
            query.push('filter[type]=' + filter.type)
        }
        if (filter.locale) {
            query.push('filter[locale]=' + filter.locale)
        }
    }

    dispatch({
        type: FETCH_BEFORE
    })

    request.get(AppRouter.GET.categories + (query ? '?' + query.join('&') : ""))
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
