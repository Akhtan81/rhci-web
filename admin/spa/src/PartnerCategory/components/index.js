import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import selectors from './selectors';
import translator from '../../translations/translator';
import FetchItems from '../actions/FetchItems';
import {FILTER_CHANGED} from '../actions';
import {isMobile, priceFormat, setTitle} from '../../Common/utils';

class Index extends React.Component {

    state = {
        isMobile: isMobile()
    }

    componentWillMount() {

        setTitle(translator('navigation_categories'))

        const {filter} = this.props.PartnerCategory

        this.props.dispatch(FetchItems(filter))

        if (typeof window !== 'undefined') {
            window.addEventListener('resize', this.resizeHandler, false);
        }
    }

    componentWillUnmount() {
        if (typeof window !== 'undefined') {
            window.removeEventListener('resize', this.resizeHandler, false);
        }
    }

    resizeHandler = () => {
        this.setState({
            isMobile: isMobile()
        })
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

        const {filter, isLoading} = this.props.PartnerCategory

        const small = this.state.isMobile

        return <div className="card my-3">

            <div className="card-header">
                <div className="row">
                    <div className="col">
                        <h4 className="m-0">{translator('navigation_categories')}</h4>
                    </div>
                    <div className="col text-right">

                        <Link to="/categories/new" className="btn btn-success btn-sm">
                            <i className="fa fa-plus"/>&nbsp;{translator('add')}
                        </Link>
                    </div>
                </div>
            </div>

            <div className="card-body">
                <div className="row">
                    <div className="col">
                        <ul className="nav nav-tabs mb-2 text-center">
                            <li className={"nav-item" + (small ? " w-100" : "")}>
                                <div
                                    className={"nav-link" + (filter && filter.type === 'junk_removal' ? ' active' : '')}
                                    onClick={this.setFilterType('junk_removal')}>

                                    {filter && filter.type === 'junk_removal' && isLoading
                                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                                        : <i className="fa fa-cubes"/>}

                                    &nbsp;{translator('order_types_junk_removal')}
                                </div>
                            </li>
                            <li className={"nav-item" + (small ? " w-100" : "")}>
                                <div className={"nav-link" + (filter && filter.type === 'recycling' ? ' active' : '')}
                                     onClick={this.setFilterType('recycling')}>

                                    {filter && filter.type === 'recycling' && isLoading
                                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                                        : <i className="fa fa-recycle"/>}

                                    &nbsp;{translator('order_types_recycling')}
                                </div>
                            </li>
                            <li className={"nav-item" + (small ? " w-100" : "")}>
                                <div className={"nav-link" + (filter && filter.type === 'donation' ? ' active' : '')}
                                     onClick={this.setFilterType('donation')}>

                                    {filter && filter.type === 'donation' && isLoading
                                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                                        : <i className="fa fa-gift"/>}

                                    &nbsp;{translator('order_types_donation')}
                                </div>
                            </li>
                            <li className={"nav-item" + (small ? " w-100" : "")}>
                                <div className={"nav-link" + (filter && filter.type === 'shredding' ? ' active' : '')}
                                     onClick={this.setFilterType('shredding')}>

                                    {filter && filter.type === 'shredding' && isLoading
                                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                                        : <i className="fa fa-stack-overflow"/>}

                                    &nbsp;{translator('order_types_shredding')}
                                </div>
                            </li>
                            <li className={"nav-item" + (small ? " w-100" : "")}>
                                <div className={"nav-link" + (filter && filter.type === 'busybee' ? ' active' : '')}
                                     onClick={this.setFilterType('busybee')}>

                                    {filter && filter.type === 'busybee' && isLoading
                                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                                        : <i className="fa fa-recycle"/>}

                                    &nbsp;{translator('order_types_busybee')}
                                </div>
                            </li>
                            <li className={"nav-item" + (small ? " w-100" : "")}>
                                <div className={"nav-link" + (filter && filter.type === 'moving' ? ' active' : '')}
                                     onClick={this.setFilterType('moving')}>

                                    {filter && filter.type === 'moving' && isLoading
                                        ? <i className="fa fa-spin fa-circle-o-notch"/>
                                        : <i className="fa fa-recycle"/>}

                                    &nbsp;{translator('order_types_moving')}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                {this.renderItems()}
            </div>
        </div>
    }

    renderItems = () => {

        const {items, isLoading} = this.props.PartnerCategory

        if (!isLoading && items.length === 0) {
            return <div className="banner">
                <h3>{translator('no_categories_title')}</h3>
                <h4>{translator('no_partner_categories_footer')}</h4>
            </div>
        }

        return <div className="table-responsive mb-3">
            <table className="table table-sm table-hover">
                <thead>
                <tr>
                    <th>{translator('name')}</th>
                    <th className="text-right">{translator('sort_order')}</th>
                    <th className="text-right">{translator('min_amount')}</th>
                    <th className="text-right">{translator('unit')}</th>
                    <th className="text-right">{translator('price')}</th>
                </tr>
                </thead>

                <tbody>{items.map(this.renderChild)}</tbody>
            </table>
        </div>
    }

    renderChild = (model, key) => {
        let prefix = ''
        for (let i = 0; i < model.category.lvl; i++) {
            prefix += '- '
        }
        return <tr key={key}>
            <td className="no-wrap">
                <Link to={'/categories/' + model.id}>{prefix}{model.category.name}</Link>
            </td>
            <td className="text-right text-nowrap">{model.category.ordering}</td>
            <td className="text-right text-nowrap">{model.minAmount}</td>
            <td className="text-right text-nowrap">{model.unit ? model.unit.name : "-"}</td>
            <td className="text-right text-nowrap">
                {model.price >= 0
                    ? <span>{priceFormat(model.price)}</span>
                    : <span className="text-muted"><i className="fa fa-ban"/></span>}
            </td>
        </tr>
    }
}

export default withRouter(connect(selectors)(Index))
