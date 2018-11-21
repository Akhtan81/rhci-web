import React from 'react';
import {Route, Switch, withRouter} from "react-router-dom";

import Unit from "../../Unit/components";
import UnitEdit from "../../UnitEdit/components";

const UnitIndex = () => {

    return <div className="container-fluid">
        <div className="row">
            <div className="col">
                <Switch>
                    <Route exact path='/units' component={Unit}/>
                    <Route exact path={'/units/new'} component={UnitEdit}/>
                    <Route path={'/units/:id'} component={UnitEdit}/>
                </Switch>
            </div>
        </div>
    </div>
}

export default withRouter(UnitIndex)
