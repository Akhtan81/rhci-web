import {all, put, select, takeEvery, throttle} from 'redux-saga/effects'
import {UPDATE_SUBSCRIPTION_SUCCESS, CANCEL_SUBSCRIPTION_SUCCESS} from '../actions'
import FetchSubscriptions from '../actions/FetchSubscriptions'

function* fetchItems() {
    yield put(FetchSubscriptions())
}

export default function* sagas() {
    yield all([
        takeEvery([
            UPDATE_SUBSCRIPTION_SUCCESS,
            CANCEL_SUBSCRIPTION_SUCCESS,
        ], fetchItems)
    ])
}
