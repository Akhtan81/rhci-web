import React from 'react';
import {connect} from "react-redux";
import {Route, Switch, withRouter} from "react-router-dom";
import selectors from "./selectors";

import CategoryList from "../../Category/components";
import CategoryEdit from "../../CategoryEdit/components";

import PartnerCategoryList from "../../PartnerCategory/components";
import PartnerCategoryEdit from "../../PartnerCategoryEdit/components";

const CategoryIndex = ({isAdmin}) => {

    const index = isAdmin ? CategoryList : PartnerCategoryList
    const edit = isAdmin ? CategoryEdit : PartnerCategoryEdit

    return <div className="container-fluid">
        <div className="row">
            <div className="col">
                <Switch>
                    <Route exact path='/categories' component={index}/>
                    <Route exact path={'/categories/new'} component={edit}/>
                    <Route path={'/categories/:id'} component={edit}/>
                </Switch>
            </div>
        </div>
    </div>
}

export default withRouter(connect(selectors)(CategoryIndex))
