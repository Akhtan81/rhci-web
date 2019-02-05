import React from 'react';
import {connect} from "react-redux";
import {Redirect, Route, Switch, withRouter} from "react-router-dom";

import selectors from "./selectors";
import Index from "../../Payment/components";

const Router = ({isAdmin}) => {

    if (!isAdmin) {
        return <Redirect to="/"/>
    }

    return <div className="container-fluid">
        <div className="row">
            <div className="col">
                <Switch>
                    <Route exact path='/payments' component={Index}/>
                </Switch>
            </div>
        </div>
    </div>
}

export default withRouter(connect(selectors)(Router))
