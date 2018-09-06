import {all, takeEvery, put, select} from 'redux-saga/effects'
import {FILTER_CHANGED} from '../actions'
import FetchItems from '../actions/FetchItems'

function* fetchItems() {
    const store = yield select(store => store.Category)

    yield put(FetchItems(store.locale, store.filter))
}

export default function* sagas() {
    yield all([
        takeEvery(FILTER_CHANGED, fetchItems),
    ])
}
