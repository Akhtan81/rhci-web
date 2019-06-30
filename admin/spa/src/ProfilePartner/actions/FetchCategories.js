import request from '../../Common/request'
import {FETCH_CATEGORIES_BEFORE, FETCH_CATEGORIES_FAILURE, FETCH_CATEGORIES_SUCCESS} from '../actions'

export default () => dispatch => {

    dispatch({
        type: FETCH_CATEGORIES_BEFORE
    })

    const query = [
        'locale=' + AppParameters.locale,
        'limit=0',
        'filter[type]=recycling',
    ]

    request.get(AppRouter.GET.categoriesTree + '?' + (query.join('&')))
        .then(({data}) => {
            dispatch({
                type: FETCH_CATEGORIES_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) {
                console.log(e);
                return
            }

            dispatch({
                type: FETCH_CATEGORIES_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
