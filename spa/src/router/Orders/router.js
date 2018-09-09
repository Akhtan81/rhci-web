import React from 'react';
import {connect} from "react-redux";
import {Route, Switch, withRouter} from "react-router-dom";
import selectors from "./selectors";

import OrderList from "../../Order/components";
import OrderEdit from "../../OrderEdit/components";

const OrderIndex = () => {

    return <div className="container-fluid">
        <div className="row">
            <div className="col">
                <Switch>
                    <Route exact path='/orders' component={OrderList}/>
                    <Route path='/orders/:id' component={OrderEdit}/>
                </Switch>
            </div>
        </div>
    </div>
}

export default withRouter(connect(selectors)(OrderIndex))
