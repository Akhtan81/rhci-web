import {delay} from 'redux-saga'
import {all, call, put, select, takeEvery} from 'redux-saga/effects'
import {MODEL_CHANGED, VALIDATE_REQUEST} from '../actions'
import Validate from '../actions/Validate'

function* requestValidation() {

    yield call(delay, 400)

    yield put({
        type: VALIDATE_REQUEST
    })
}

function* runValidation() {
    const {model, changes} = yield select(store => store.PartnerCategoryEdit)

    yield put(Validate(model, changes))
}

export default function* sagas() {
    yield all([
        takeEvery(MODEL_CHANGED, requestValidation),

        takeEvery(VALIDATE_REQUEST, runValidation)
    ])
}
