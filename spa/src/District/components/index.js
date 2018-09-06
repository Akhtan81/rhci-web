import React from 'react';
import {Route, Switch} from "react-router-dom";
import DistrictList from "./DistrictList";

const DistrictIndex = ({match}) => <div className="container">
    <div className="row">
        <div className="col">
            <Switch>
                <Route exact path={match.url} component={DistrictList}/>
            </Switch>
        </div>
    </div>
</div>

export default DistrictIndex
