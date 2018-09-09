import {all, put, select, takeEvery} from 'redux-saga/effects'
import {FILTER_CLEAR, PAGE_CHANGED} from '../actions'
import FetchItems from "../actions/FetchItems";

function* fetchItems({payload}) {
    const store = yield select(store => store.Order)

    yield put(FetchItems(store.filter, payload))
}

export default function* sagas() {
    yield all([
        takeEvery([
            PAGE_CHANGED, FILTER_CLEAR
        ], fetchItems),
    ])
}
