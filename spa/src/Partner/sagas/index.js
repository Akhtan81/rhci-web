import {all, put, takeEvery} from 'redux-saga/effects'
import {FILTER_CHANGED} from '../actions'
import FetchRegions from "../actions/FetchRegions";
import FetchCities from "../actions/FetchCities";
import FetchDistricts from "../actions/FetchDistricts";

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
        takeEvery(FILTER_CHANGED, fetchGeoItems),
    ])
}
