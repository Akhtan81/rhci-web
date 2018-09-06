import React from 'react';
import {Route, Switch} from "react-router-dom";
import CategoryList from "./CategoryList";

const CategoryIndex = ({match}) => <div className="container-fluid">
    <div className="row">
        <div className="col">
            <Switch>
                <Route exact path={match.url} component={CategoryList}/>
            </Switch>
        </div>
    </div>
</div>

export default CategoryIndex
