import React from "react";
import {BrowserRouter, Redirect, Route, Switch} from "react-router-dom";

import Login from "../Login/components/index";
import PartnerRegister from "../PartnerRegister/components/index";
import RegisterIntroduction from "../PartnerRegister/components/RegisterIntroduction";
import PasswordReset from "../PasswordReset/components/index";
import PasswordSet from "../PasswordSet/components/index";
import Layout from "./Layout";
import Privacy from "../Privacy/components";
import Terms from "../Terms/components";

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
            <Route path='/privacy' exact component={Privacy}/>
            <Route path='/terms' exact component={Terms}/>
            <Route path='/introduction' exact component={RegisterIntroduction}/>
            <Route path='/register' exact component={PartnerRegister}/>
            <Route path='/reset-password' exact component={PasswordReset}/>
            <Route path='/users/:token/password' component={PasswordSet}/>
            <PrivateRoute component={Layout}/>
        </Switch>
    </BrowserRouter>
}

export default createRouter;
