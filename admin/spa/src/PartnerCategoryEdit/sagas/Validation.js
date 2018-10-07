import {delay} from 'redux-saga'
import {all, call, put, select, takeEvery} from 'redux-saga/effects'
import {CATEGORY_CHANGED, VALIDATE_REQUEST} from '../actions'
import Validate from '../actions/Validate'

function* requestValidation() {

    yield call(delay, 400)

    yield put({
        type: VALIDATE_REQUEST
    })
}

function* runValidation() {
    const {model, changes} = yield select(store => store.CategoryEdit)

    yield put(Validate(model, changes))
}

export default function* sagas() {
    yield all([
        takeEvery(CATEGORY_CHANGED, requestValidation),

        takeEvery(VALIDATE_REQUEST, runValidation)
    ])
}
