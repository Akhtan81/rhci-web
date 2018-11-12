import {all, put, select, takeEvery} from 'redux-saga/effects'
import {FILTER_CHANGED, FILTER_CLEAR, PAGE_CHANGED} from '../actions'
import FetchRegions from "../actions/FetchRegions";
import FetchCities from "../actions/FetchCities";
import FetchDistricts from "../actions/FetchDistricts";
import FetchItems from "../actions/FetchItems";

function* fetchItems({payload}) {
    const store = yield select(store => store.Partner)
    const page = payload && payload.page > 0 ? payload.page : store.page

    yield put(FetchItems(store.filter, page))
}

function* fetchGeoItems({payload}) {
    if (payload.country) {

        yield put(FetchRegions(payload.country))

    } else if (payload.region) {

        yield put(FetchCities(payload.region))

    } else if (payload.city) {

        yield put(FetchDistricts(payload.city))
    }
}

export default function* sagas() {
    yield all([

        takeEvery([
            PAGE_CHANGED,
            FILTER_CLEAR
        ], fetchItems),

        takeEvery(FILTER_CHANGED, fetchGeoItems),
    ])
}
