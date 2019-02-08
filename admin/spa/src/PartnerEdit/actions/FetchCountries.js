import request from '../../Common/request'
import {FETCH_COUNTRIES_BEFORE, FETCH_COUNTRIES_FAILURE, FETCH_COUNTRIES_SUCCESS} from '../actions'

export default () => dispatch => {

    dispatch({
        type: FETCH_COUNTRIES_BEFORE
    })

    const query = [
        'limit=0',
        'filter[type]=recycling',
        'filter[lvl]=0'
    ]

    request.get(AppRouter.GET.countries + '?' + (query.join('&')))
        .then(({data}) => {
            dispatch({
                type: FETCH_COUNTRIES_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            console.log(e);
            if (!e.response) {
                console.log(e);
                return
            }

            dispatch({
                type: FETCH_COUNTRIES_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
