import request from '../../Common/request'
import {FETCH_CATEGORIES_BEFORE, FETCH_CATEGORIES_FAILURE, FETCH_CATEGORIES_SUCCESS} from '../actions'

export default () => dispatch => {

    dispatch({
        type: FETCH_CATEGORIES_BEFORE
    })

    const query = [
        'limit=0',
        'filter[type]=recycling',
        'filter[lvl]=0'
    ]

    request.get(AppRouter.GET.categoriesTree + '?' + (query.join('&')))
        .then(({data}) => {
            dispatch({
                type: FETCH_CATEGORIES_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            console.log(e);
            if (!e.response) return

            dispatch({
                type: FETCH_CATEGORIES_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
