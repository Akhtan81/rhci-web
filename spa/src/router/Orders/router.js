import React from 'react';
import {connect} from "react-redux";
import {Redirect, Route, Switch, withRouter} from "react-router-dom";
import selectors from "./selectors";

import OrderList from "../../Order/components";
import PartnerOrderList from "../../PartnerOrder/components";
import OrderEdit from "../../OrderEdit/components";

const OrderIndex = ({isAdmin, isPartner, partner}) => {

    if (isPartner && !partner.accountId) {
        return <Redirect to="/profile"/>
    }

    const index = isAdmin ? OrderList : PartnerOrderList;

    return <div className="container-fluid">
        <div className="row">
            <div className="col">
                <Switch>
                    <Route exact path='/orders' component={index}/>
                    <Route path='/orders/:id' component={OrderEdit}/>
                </Switch>
            </div>
        </div>
    </div>
}

export default withRouter(connect(selectors)(OrderIndex))
