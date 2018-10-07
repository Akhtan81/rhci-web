import React from 'react';
import {connect} from "react-redux";
import {Redirect, Route, Switch, withRouter} from "react-router-dom";

import selectors from "./selectors";
import PartnerList from "../../Partner/components";
import PartnerEdit from "../../PartnerEdit/components";

const PartnerIndex = ({isAdmin}) => {

    if (!isAdmin) {
        return <Redirect to="/"/>
    }

    return <div className="container-fluid">
        <div className="row">
            <div className="col">
                <Switch>
                    <Route exact path='/partners' component={PartnerList}/>
                    <Route exact path={'/partners/new'} component={PartnerEdit}/>
                    <Route path={'/partners/:id'} component={PartnerEdit}/>
                </Switch>
            </div>
        </div>
    </div>
}

export default withRouter(connect(selectors)(PartnerIndex))
