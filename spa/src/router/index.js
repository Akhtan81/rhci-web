import React from "react";
import {BrowserRouter, Redirect, Route, Switch} from "react-router-dom";

import Login from "../Login/components/index";
import PartnerRegister from "../PartnerRegister/components/index";
import Layout from "./Layout";

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
            <Route path='/login' exact component={Login}/>
            <Route path='/register' exact component={PartnerRegister}/>
            <PrivateRoute component={Layout}/>
        </Switch>
    </BrowserRouter>
}

export default createRouter;
