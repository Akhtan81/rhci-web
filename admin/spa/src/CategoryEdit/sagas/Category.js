import {all, put, takeEvery} from 'redux-saga/effects'
import {FETCH_SUCCESS, MODEL_CHANGED} from '../actions'
import FetchItems from '../../Category/actions/FetchItems';

function* fetchItems({payload}) {

    if (payload.type === undefined) return

    yield put(FetchItems({
        locale: AppParameters.locale,
        type: payload.type
    }))
}

export default function* sagas() {
    yield all([
        takeEvery([MODEL_CHANGED, FETCH_SUCCESS], fetchItems)
    ])
}
