import {all, put, select, takeEvery, throttle} from 'redux-saga/effects'
import {FETCH_SUCCESS, MODEL_CHANGED, UPLOAD_MEDIA_SUCCESS, VALIDATE_REQUEST} from '../actions'
import Validate from '../actions/Validate'

function* requestValidation() {
    yield put({
        type: VALIDATE_REQUEST
    })
}

function* runValidation() {
    const {model, changes} = yield select(store => store.PartnerEdit)

    yield put(Validate(model, changes))
}

export default function* sagas() {
    yield all([
        throttle(400, MODEL_CHANGED, requestValidation),

        takeEvery([
            FETCH_SUCCESS,
            UPLOAD_MEDIA_SUCCESS,
        ], requestValidation),

        takeEvery([
            VALIDATE_REQUEST,
        ], runValidation)
    ])
}
