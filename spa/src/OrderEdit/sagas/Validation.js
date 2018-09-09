import {all, takeEvery, put, select, throttle} from 'redux-saga/effects'
import {MODEL_CHANGED, VALIDATE_REQUEST} from '../actions'
import Validate from '../actions/Validate'

function* requestValidation() {
    yield put({
        type: VALIDATE_REQUEST
    })
}

function* runValidation() {
    const {model, changes} = yield select(store => store.OrderEdit)

    yield put(Validate(model, changes))
}

export default function* sagas() {
    yield all([
        throttle(400, MODEL_CHANGED, requestValidation),

        takeEvery(VALIDATE_REQUEST, runValidation)
    ])
}
