import React from 'react';
import {connect} from "react-redux";
import {Route, Switch, withRouter} from "react-router-dom";
import selectors from "./selectors";

import CategoryList from "../../Category/components";
import CategoryEdit from "../../CategoryEdit/components";

import PartnerCategoryList from "../../PartnerCategory/components";
import PartnerCategoryEdit from "../../PartnerCategoryEdit/components";

const CategoryIndex = ({isAdmin, isPartner}) => {

    if (isAdmin) {
        return <div className="container-fluid">
            <div className="row">
                <div className="col">
                    <Switch>
                        <Route exact path='/categories' component={CategoryList}/>
                        <Route exact path={'/categories/new'} component={CategoryEdit}/>
                        <Route path={'/categories/:id'} component={CategoryEdit}/>
                    </Switch>
                </div>
            </div>
        </div>
    } else if (isPartner) {
        return <div className="container-fluid">
            <div className="row">
                <div className="col">
                    <Switch>
                        <Route exact path='/categories' component={PartnerCategoryList}/>
                        <Route exact path={'/categories/new'} component={PartnerCategoryEdit}/>
                        <Route path={'/categories/:id'} component={PartnerCategoryEdit}/>
                    </Switch>
                </div>
            </div>
        </div>
    } else {
        return null
    }
}

export default withRouter(connect(selectors)(CategoryIndex))
