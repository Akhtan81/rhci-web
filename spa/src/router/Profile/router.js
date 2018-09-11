import React from 'react';
import {connect} from "react-redux";
import {Route, Switch, withRouter} from "react-router-dom";
import selectors from "./selectors";

import Profile from "../../Profile/components";

const ProfileIndex = ({isPartner}) => {

    if (!isPartner) {
        return null
    }

    return <div className="container-fluid">
        <div className="row">
            <div className="col">
                <Switch>
                    <Route exact path='/profile' component={Profile}/>
                </Switch>
            </div>
        </div>
    </div>
}

export default withRouter(connect(selectors)(ProfileIndex))
