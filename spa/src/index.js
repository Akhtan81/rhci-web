import React from 'react';
import {render} from 'react-dom';
import {Provider} from 'react-redux';

import Store from './store'
import Router from "./router";

const id = 'app';
const app = document.getElementById(id);
if (!app) {
    throw 'No DOM element with id: ' + id
}

render(
    <Provider store={Store}>
        {Router(Store)}
    </Provider>,
    app
);
