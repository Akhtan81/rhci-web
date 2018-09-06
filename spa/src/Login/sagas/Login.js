import {put, select, takeEvery, throttle, all} from 'redux-saga/effects'

import {history} from '../../router/BrowserRouter'
import * as Actions from '../actions'
import Validate from '../actions/Validate'

function* redirectToLogin() {
    history.push('/login')
}

function* redirectToIndex() {
    history.push('/')
}

function* validateRequest() {
    yield put({
        type: Actions.LOGIN_VALIDATE_REQUEST
    })
}

function* validate() {
    const store = yield select()

    yield put(Validate(store.Login))
}

export default function* sagas() {
    yield all([

        takeEvery([
            Actions.FETCH_CURRENT_USER_SUCCESS,
            Actions.LOGIN_SUCCESS,
        ], redirectToIndex),

        takeEvery([
            Actions.FETCH_CURRENT_USER_FAILURE,
            Actions.LOGIN_FAILURE,
        ], redirectToLogin),

        takeEvery([
            Actions.LOGIN_CREDENTIALS_CHANGED
        ], validateRequest),

        throttle(400, Actions.LOGIN_VALIDATE_REQUEST, validate),
    ])
}
