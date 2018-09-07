import request from 'axios'
import {FETCH_BEFORE, FETCH_FAILURE, FETCH_SUCCESS} from '../actions'

export default id => dispatch => {

    dispatch({
        type: FETCH_BEFORE
    })

    request.get(AppRouter.GET.category.replace('__ID__', id))
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
