import {all, put, select, takeEvery} from 'redux-saga/effects'
import {FILTER_CHANGED} from '../actions'
import FetchItems from '../actions/FetchItems'

function* fetchItems() {
    const store = yield select(store => store.Unit)

    yield put(FetchItems(store.filter))
}

export default function* sagas() {
    yield all([
        takeEvery(FILTER_CHANGED, fetchItems),
    ])
}
