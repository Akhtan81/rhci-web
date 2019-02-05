import {all, put, select, takeEvery} from 'redux-saga/effects'
import {FILTER_CLEAR, PAGE_CHANGED, FETCH_REQUEST} from '../actions'
import FetchItems from "../actions/FetchItems";

function* fetchItems({payload}) {
    const store = yield select(store => store.Payment)
    const page = payload && payload.page > 0 ? payload.page : store.page

    yield put(FetchItems(store.filter, page))
}

export default function* sagas() {
    yield all([

        takeEvery([
            FETCH_REQUEST,
            PAGE_CHANGED,
            FILTER_CLEAR
        ], fetchItems),
    ])
}
