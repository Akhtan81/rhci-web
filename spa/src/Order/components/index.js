import React from 'react';
import {Route, Switch} from "react-router-dom";
import OrderList from "./OrderList";

const OrderIndex = ({match}) => <div className="container">
    <div className="row">
        <div className="col">
            <Switch>
                <Route exact path={match.url} component={OrderList}/>
            </Switch>
        </div>
    </div>
</div>

export default OrderIndex
