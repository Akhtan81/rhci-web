import request from '../../Common/request'
import {SAVE_BEFORE, SAVE_FAILURE, SAVE_SUCCESS} from '../actions'

const parseBeforeSubmit = model => {
    const data = {...model}

    if (data.user) {
        if (data.user.avatar && data.user.avatar.id) {
            data.user.avatar = data.user.avatar.id
        }
    }

    if (data.country && data.country.id) {
        data.country = data.country.id
    }

    if (data.postalCodesRecycling || data.postalCodesJunkRemoval || data.postalCodesShredding || data.postalCodesDonation) {
        data.postalCodes = []

        if (data.postalCodesRecycling) {
            const items = data.postalCodesRecycling.split(',')

            data.postalCodes = data.postalCodes.concat(items.map(postalCode => ({
                type: 'recycling',
                postalCode
            })))
        }

        if (data.postalCodesJunkRemoval) {
            const items = data.postalCodesJunkRemoval.split(',')

            data.postalCodes = data.postalCodes.concat(items.map(postalCode => ({
                type: 'junk_removal',
                postalCode
            })))
        }

        if (data.postalCodesShredding) {
            const items = data.postalCodesShredding.split(',')

            data.postalCodes = data.postalCodes.concat(items.map(postalCode => ({
                type: 'shredding',
                postalCode
            })))
        }

        if (data.postalCodesDonation) {
            const items = data.postalCodesDonation.split(',')

            data.postalCodes = data.postalCodes.concat(items.map(postalCode => ({
                type: 'donation',
                postalCode
            })))
        }
    }

    return data
}

export default (model, callback) => dispatch => {

    const data = parseBeforeSubmit(model)

    dispatch({
        type: SAVE_BEFORE
    })

    let promise
    if (data.id > 0) {
        promise = request.put(AppRouter.PUT.partner.replace('__ID__', data.id), data)
    } else {
        promise = request.post(AppRouter.POST.partner, data)
    }

    promise
        .then(({data}) => {
            dispatch({
                type: SAVE_SUCCESS,
                payload: data
            })

            if (callback) {
                callback()
            }
        })
        .catch(e => {
            console.log(e);
            if (!e.response) return

            dispatch({
                type: SAVE_FAILURE,
                payload: {
                    status: e.response.status,
                    data: e.response.data
                }
            })
        })
}
