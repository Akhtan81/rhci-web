import {all, put, takeEvery} from 'redux-saga/effects'
import {ADD_TRANSLATION, FETCH_SUCCESS, MODEL_CHANGED} from '../actions'
import FetchItems from '../../Category/actions/FetchItems';

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

function* fetchItems({payload}) {

    if (payload.type === undefined) return

    yield put(FetchItems({
        locale: AppParameters.locale,
        type: payload.type
    }))
}

export default function* sagas() {
    yield all([
        takeEvery([MODEL_CHANGED, FETCH_SUCCESS], fetchItems),

        takeEvery(FETCH_SUCCESS, addLocales)
    ])
}
