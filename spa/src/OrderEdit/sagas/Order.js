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

export default function* sagas() {
    yield all([
        takeEvery(MODEL_CHANGED, approveScheduledAtIfChanged),
    ])
}
