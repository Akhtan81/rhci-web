import {all, put, takeEvery} from 'redux-saga/effects'
import {FETCH_COUNTRIES_SUCCESS, MODEL_CHANGED} from '../actions'

function* setDefaultCountry({payload}) {
    const defaultCountry = payload.items.find(country =>
        country.name === AppParameters.defaultCountryName
        && country.locale === AppParameters.locale
    )

    if (defaultCountry) {
        yield put({
            type: MODEL_CHANGED,
            payload: {
                country: defaultCountry.id
            }
        })
    }
}

export default function* sagas() {
    yield all([
        takeEvery(FETCH_COUNTRIES_SUCCESS, setDefaultCountry)
    ])
}
