import request from 'axios'
import {FETCH_BEFORE, FETCH_FAILURE, FETCH_SUCCESS} from '../actions'

export default (locale, filter) => dispatch => {

    const query = [];

    if (filter) {
        if (filter.type) {
            query.push('filter[type]=' + filter.type)
        }
    }

    dispatch({
        type: FETCH_BEFORE
    })

    request.get(AppRouter.GET.categories.replace('__LOCALE__', locale)
        + (query ? '?' + query.join('&') : ""))
        .then(({data}) => {
            dispatch({
                type: FETCH_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            console.log(e);
            dispatch({
                type: FETCH_FAILURE,
            })
        })
}
