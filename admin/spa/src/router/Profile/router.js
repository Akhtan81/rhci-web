import React from 'react';
import {connect} from "react-redux";
import {Route, Switch, withRouter} from "react-router-dom";
import selectors from "./selectors";

import ProfilePartner from "../../ProfilePartner/components";
import ProfileUser from "../../ProfileUser/components";

const ProfileIndex = ({isPartner}) => {

    const view = isPartner?ProfilePartner:ProfileUser;
    return <div className="container-fluid">
        <div className="row">
            <div className="col">
                <Switch>
                    <Route exact path='/profile' component={view}/>
                </Switch>
            </div>
        </div>
    </div>
}

export default withRouter(connect(selectors)(ProfileIndex))
