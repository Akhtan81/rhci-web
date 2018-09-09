import {all, put, takeEvery, select} from 'redux-saga/effects'
import {FILTER_CHANGED, PAGE_CHANGED, FILTER_CLEAR} from '../actions'
import FetchRegions from "../actions/FetchRegions";
import FetchCities from "../actions/FetchCities";
import FetchDistricts from "../actions/FetchDistricts";
import FetchItems from "../actions/FetchItems";

function* fetchItems({payload}) {
    const store = yield select(store => store.Partner)

    yield put(FetchItems(store.filter, payload))
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
