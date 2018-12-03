import {all, put, select, takeEvery, throttle} from 'redux-saga/effects'
import {MODEL_CHANGED, VALIDATE_REQUEST} from '../actions'
import Validate from '../actions/Validate'
import {UPLOAD_MEDIA_SUCCESS} from "../actions";

function* requestValidation() {
    yield put({
        type: VALIDATE_REQUEST
    })
}

function* runValidation() {
    const {model, changes} = yield select(store => store.ProfilePartner)

    yield put(Validate(model, changes))
}

export default function* sagas() {
    yield all([
        throttle(400, MODEL_CHANGED, requestValidation),

        takeEvery([
            UPLOAD_MEDIA_SUCCESS,
        ], requestValidation),

        takeEvery([
            VALIDATE_REQUEST,
        ], runValidation)
    ])
}
