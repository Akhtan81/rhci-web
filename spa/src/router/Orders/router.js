import React from 'react';
import {connect} from "react-redux";
import {Route, Switch, withRouter} from "react-router-dom";
import selectors from "./selectors";

import CategoryList from "../../Category/components";
import CategoryEdit from "../../CategoryEdit/components";

const OrderIndex = ({isAdmin, isPartner}) => {

    if (isAdmin) {
        return <div className="container-fluid">
            <div className="row">
                <div className="col">
                    <Switch>
                        <CategorySwitch exact path='/orders' component={CategoryList}/>
                        <Route exact path={'/orders/new'} component={CategoryEdit}/>
                        <Route path={'/orders/:id'} component={CategoryEdit}/>
                    </Switch>
                </div>
            </div>
        </div>
    } else if (isPartner) {
        return <div className="container-fluid">
            <div className="row">
                <div className="col">
                    <Switch>
                        <CategorySwitch exact path='/orders' component={CategoryList}/>
                        <Route exact path={'/orders/new'} component={CategoryEdit}/>
                        <Route path={'/orders/:id'} component={CategoryEdit}/>
                    </Switch>
                </div>
            </div>
        </div>
    } else {
        return null
    }
}

export default withRouter(connect(selectors)(OrderIndex))
