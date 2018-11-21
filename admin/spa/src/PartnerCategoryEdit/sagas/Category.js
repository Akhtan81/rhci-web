import {all, put, takeEvery} from 'redux-saga/effects'
import FetchItems from '../../Category/actions/FetchItems'
import {MODEL_CHANGED} from '../actions'

function* fetchItems({payload}) {

    if (payload.type !== undefined) {

        yield put(FetchItems({
            type: payload.type,
            locale: AppParameters.locale
        }))
    }
}

export default function* sagas() {
    yield all([
        takeEvery(MODEL_CHANGED, fetchItems),
    ])
}
