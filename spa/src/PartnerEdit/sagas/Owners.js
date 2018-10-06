import {all, takeEvery, put, select, throttle} from 'redux-saga/effects'
import {MODEL_CHANGED, VALIDATE_REQUEST, UPLOAD_MEDIA_SUCCESS, FETCH_SUCCESS} from '../actions'
import FetchOwners from '../actions/FetchOwners'

function* run({payload}) {

    if (payload.requests !== undefined) {
        if (payload.requests.length > 0) {
            yield put(FetchOwners(payload.requests))
        }
    }
}

export default function* sagas() {
    yield all([
        throttle(400, FETCH_SUCCESS, run),
    ])
}
