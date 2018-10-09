import {all, put, select, takeEvery, throttle} from 'redux-saga/effects'

import * as Actions from '../actions'
import Validate from '../actions/Validate'

function* validateRequest() {
    yield put({
        type: Actions.LOGIN_VALIDATE_REQUEST
    })
}

function* validate() {
    const store = yield select()

    yield put(Validate(store.Login))
}

function* updateAppParameters({payload}) {
    window.location = '/'
}

export default function* sagas() {
    yield all([

        takeEvery(Actions.LOGIN_SUCCESS, updateAppParameters),

        takeEvery([
            Actions.LOGIN_CREDENTIALS_CHANGED
        ], validateRequest),

        throttle(400, Actions.LOGIN_VALIDATE_REQUEST, validate),
    ])
}
