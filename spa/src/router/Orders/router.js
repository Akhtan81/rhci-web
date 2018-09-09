import React from 'react';
import {connect} from "react-redux";
import {Route, Switch, withRouter} from "react-router-dom";
import selectors from "./selectors";

import OrderList from "../../Order/components";

const OrderIndex = ({isAdmin, isPartner}) => {

    // const index = isAdmin ? CategoryList : PartnerCategoryList
    // const edit = isAdmin ? CategoryEdit : PartnerCategoryEdit

    return <div className="container-fluid">
        <div className="row">
            <div className="col">
                <Switch>
                    <Route exact path='/orders' component={OrderList}/>
                    {/*<Route exact path={'/categories/new'} component={edit}/>*/}
                    {/*<Route path={'/categories/:id'} component={edit}/>*/}
                </Switch>
            </div>
        </div>
    </div>
}

export default withRouter(connect(selectors)(OrderIndex))
