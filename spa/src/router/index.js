import React from "react";
import {BrowserRouter, Redirect, Route, Switch} from "react-router-dom";

import Login from "../Login/components/index";
import DashboardLayout from "../Common/components/Layout";

const createRouter = (store) => {

    const PrivateRoute = ({component: Component, ...rest}) => (
        <Route {...rest} render={(props) => (
            store.getState().User.isAuthenticated === true
                ? <Component {...props} />
                : <Redirect to='/login'/>
        )}/>
    )

    return <BrowserRouter>
        <Switch>
            <Route path='/login' excat component={Login}/>
            <PrivateRoute component={DashboardLayout}/>
        </Switch>
    </BrowserRouter>
}

export default createRouter;
