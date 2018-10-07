import request from '../../Common/request'
import {UPLOAD_MEDIA_BEFORE, UPLOAD_MEDIA_FAILURE, UPLOAD_MEDIA_SUCCESS} from '../actions'

export default file => dispatch => {

    const data = new FormData()
    data.append('file', file)

    dispatch({
        type: UPLOAD_MEDIA_BEFORE
    })

    request.post(AppRouter.POST.media, data)
        .then(({data}) => {
            dispatch({
                type: UPLOAD_MEDIA_SUCCESS,
                payload: data
            })
        })
        .catch(e => {
            if (!e.response) return

            dispatch({
                type: UPLOAD_MEDIA_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
