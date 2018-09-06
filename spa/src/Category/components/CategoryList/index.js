import React from 'react';
import {connect} from 'react-redux';
import {Link} from 'react-router-dom';
import selectors from '../selectors';
import translator from '../../../translations/translator';
import FetchItems from '../../actions/FetchItems';
import {FILTER_CHANGED} from '../../actions';
import Item from './Item';

class Index extends React.Component {

    componentWillMount() {

        const {locale, filter} = this.props.Category

        this.props.dispatch(FetchItems(locale, filter))
    }

    setLocale = e => {
        this.props.dispatch({
            type: FILTER_CHANGED,
            payload: {
                locale: e.target.value
            }
        })
    }

    setFilterType = type => () => {
        this.props.dispatch({
            type: FILTER_CHANGED,
            payload: {type}
        })
    }

    render() {

        const {items, filter, locale} = this.props.Category

        return <div className="bgc-white bd bdrs-3 p-20 mB-20">

            <div className="row">
                <div className="col">
                    <h4 className="page-title">
                        {translator('navigation_categories')}
                    </h4>
                </div>
                <div className="col text-right">
                    <select name="locale"
                            className="form-control-sm"
                            value={locale}
                            onChange={this.setLocale}>
                        {AppParameters.locales.map((code, i) => <option key={i} value={code}>{code}</option>)}
                    </select>
                </div>
            </div>

            <div className="row">
                <div className="col">
                    <div className="form-group">
                        <Link to="/categories/new" className="btn btn-primary">
                            <i className="fa fa-plus"/>&nbsp;{translator('add')}
                        </Link>
                    </div>
                </div>
            </div>

            <ul className="nav nav-tabs mb-2">
                <li className="nav-item">
                    <div className={"nav-link" + (filter && filter.type === 'junk_removal' ? ' active' : '')}
                         onClick={this.setFilterType('junk_removal')}>
                        <i className="fa fa-cubes"/>&nbsp;{translator('order_types_junk_removal')}
                    </div>
                </li>
                <li className="nav-item">
                    <div className={"nav-link" + (filter && filter.type === 'recycling' ? ' active' : '')}
                         onClick={this.setFilterType('recycling')}>
                        <i className="fa fa-recycle"/>&nbsp;{translator('order_types_recycling')}
                    </div>
                </li>
                <li className="nav-item">
                    <div className={"nav-link" + (filter && filter.type === 'shredding' ? ' active' : '')}
                         onClick={this.setFilterType('shredding')}>
                        <i className="fa fa-stack-overflow"/>&nbsp;{translator('order_types_shredding')}
                    </div>
                </li>
            </ul>

            <ul className="sidebar-menu h-auto">
                {items.map((model, i) => <li key={i} className="nav-item">
                    <Item model={model}/>
                </li>)}
            </ul>
        </div>
    }
}

export default connect(selectors)(Index)
