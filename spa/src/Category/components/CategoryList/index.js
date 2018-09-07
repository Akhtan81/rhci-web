import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import selectors from './selectors';
import translator from '../../../translations/translator';
import FetchItems from '../../actions/FetchItems';
import {FILTER_CHANGED} from '../../actions';

class Index extends React.Component {

    componentWillMount() {

        const {filter} = this.props.Category

        this.props.dispatch(FetchItems(filter))
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

        const {items, filter} = this.props.Category

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
                            value={filter.locale || ''}
                            onChange={this.setLocale}>
                        {AppParameters.locales.map((code, i) => <option key={i} value={code}>{code}</option>)}
                    </select>
                </div>
            </div>

            <div className="row">
                <div className="col">
                    <div className="form-group">
                        <Link to="/categories/new" className="btn btn-success btn-sm">
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

            <table className="table table-sm table-hover">
                <thead>
                <tr>
                    <th className="text-left">{translator('name')}</th>
                    <th className="text-center">{translator('is_selectable')}</th>
                    <th className="text-right">{translator('price')}</th>
                    <th className="text-right">{translator('created_at')}</th>
                </tr>
                </thead>

                <tbody>{items.map(this.renderChild)}</tbody>
            </table>
        </div>
    }

    renderChild = (model, key) => {
        return <tr key={key}>
            <td className="no-wrap" style={{paddingLeft: (model.lvl * 20) + 'px'}}>
                <Link to={'/categories/' + model.id}>{model.name}</Link>
            </td>
            <td className="text-center text-nowrap">
                {model.isSelectable ? <i className="fa fa-check c-green-500"/> : <i className="fa fa-times c-red-500"/>}
            </td>
            <td className="text-right text-nowrap">
                {model.hasPrice ? <span>{(model.price / 100).toFixed(2)}</span>
                    : <span className="text-muted mr-2">
                        <i className="fa fa-ban"/>
                    </span>}
            </td>

            <td className="text-right text-nowrap">{model.createdAt}</td>
        </tr>
    }
}

export default withRouter(connect(selectors)(Index))
