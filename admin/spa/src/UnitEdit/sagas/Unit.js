import {all, put, takeEvery} from 'redux-saga/effects'
import {ADD_TRANSLATION, FETCH_SUCCESS} from '../actions'

function* addLocales() {

    for (let i = 0; i < AppParameters.locales.length; i++) {

        let locale = AppParameters.locales[i]

        yield put({
            type: ADD_TRANSLATION,
            payload: {
                locale,
                name: null
            }
        })
    }
}


export default function* sagas() {
    yield all([
        takeEvery(FETCH_SUCCESS, addLocales)
    ])
}
