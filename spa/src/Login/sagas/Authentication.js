import {all, takeEvery, put} from 'redux-saga/effects'
import {OFFLINE} from "../actions";

function* markOffline() {
    yield put({
        type: OFFLINE
    })
}

const filter = action => {
    if (action.type.indexOf('_FAILURE') !== -1) {
        return action.payload.status === 401
    }

    return false
}

export default function* sagas() {
    yield all([
        takeEvery(filter, markOffline),
    ])
}
