import {all, put, takeEvery} from 'redux-saga/effects'
import FetchRegions from "../../Partner/actions/FetchRegions";
import FetchCities from "../../Partner/actions/FetchCities";
import FetchDistricts from "../../Partner/actions/FetchDistricts";
import {MODEL_CHANGED} from "../../PartnerEdit/actions";

function* fetchGeoItems({payload}) {
    if (payload.country) {

        yield put(FetchRegions(payload.country.id))

    } else if (payload.region) {

        yield put(FetchCities(payload.region.id))

    } else if (payload.city) {

        yield put(FetchDistricts(payload.city.id))
    }
}

export default function* sagas() {
    yield all([
        takeEvery(MODEL_CHANGED, fetchGeoItems),
    ])
}