import request from 'axios'
import {FETCH_CURRENT_USER_BEFORE, FETCH_CURRENT_USER_SUCCESS, FETCH_CURRENT_USER_FAILURE} from '../../Login/actions'

export default () => dispatch =>  {

    dispatch({
        type: FETCH_CURRENT_USER_BEFORE
    })

    request.get(AppRouter.GET.currentUser)
        .then(({data}) => {
            dispatch({
                type: FETCH_CURRENT_USER_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            console.log(e);
            dispatch({
                type: FETCH_CURRENT_USER_FAILURE,
            })
        })
}
