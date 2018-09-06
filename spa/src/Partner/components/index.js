import React from 'react';
import {Route, Switch} from "react-router-dom";
import PartnerList from './PartnerList';

const PartnerIndex = ({match}) => <div className="container">
    <div className="row">
        <div className="col">
            <Switch>
                <Route exact path={match.url} component={PartnerList}/>
            </Switch>
        </div>
    </div>
</div>

export default PartnerIndex
