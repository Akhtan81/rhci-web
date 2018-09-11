import {all, put, takeEvery} from 'redux-saga/effects'
import {MODEL_CHANGED} from '../actions'

function* approveScheduledAtIfChanged({payload}) {
    if (payload.scheduledAt !== undefined) {
        yield put({
            type: MODEL_CHANGED,
            payload: {
                isScheduleApproved: true
            }
        })
    }
}

function* approvePriceIfChanged({payload}) {
    if (payload.price !== undefined) {
        yield put({
            type: MODEL_CHANGED,
            payload: {
                isPriceApproved: true
            }
        })
    }
}

export default function* sagas() {
    yield all([
        takeEvery(MODEL_CHANGED, approveScheduledAtIfChanged),
        takeEvery(MODEL_CHANGED, approvePriceIfChanged),
    ])
}
