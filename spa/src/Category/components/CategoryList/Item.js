import React from 'react';
import {Link} from 'react-router-dom';
import {connect} from 'react-redux';
import translator from '../../../translations/translator';
import selectors from '../selectors';

class Item extends React.Component {

    render() {

        const {model} = this.props

        return <div>

            <Link to={"/categories/" + model.id} className="sidebar-link">
                <div className="row no-gutters">
                    <div className="col text-left">
                        <h5>{model.name}</h5>
                    </div>
                    <div className="col text-right">

                        {!model.isSelectable && <small className="text-muted mr-2">
                            <i className="fa fa-ban"/>&nbsp;{translator('category_not_selectable')}
                        </small>}

                        {model.hasPrice ? <h5 className="mr-2">{(model.price / 100).toFixed(2)}</h5>
                            : <small className="text-muted mr-2">
                                <i className="fa fa-ban"/>&nbsp;{translator('category_no_price')}
                            </small>}
                    </div>
                </div>
            </Link>

            {
                model.children ?
                    <ul className={"sidebar-menu h-auto"} style={{marginLeft: ((model.lvl + 1) * 40) + 'px'}}>
                        {model.children.map((model, i) => <li key={i} className="nav-item">
                            <Item model={model}/>
                        </li>)}
                    </ul>
                    : null
            }
        </div>
    }
}

export default connect(selectors)(Item)
