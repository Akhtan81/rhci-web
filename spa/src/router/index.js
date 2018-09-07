import React from "react";
import {BrowserRouter} from "react-router-dom";

import Login from "../Login/components/index";
import DashboardLayout from "../Common/components/Layout";

const createRouter = (store) => {

    return <BrowserRouter>
        {!store.getState().User.isAuthenticated ? <Login/> : <DashboardLayout/>}
    </BrowserRouter>
}

export default createRouter;
