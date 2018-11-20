import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import selectors from './selectors';
import translator from '../../translations/translator';
import FetchItems from '../actions/FetchItems';
import Paginator from '../../Common/components/Paginator';
import {FILTER_CHANGED, FILTER_CLEAR, PAGE_CHANGED} from '../actions';
import FetchCountries from "../actions/FetchCountries";
import {dateFormat, setTitle} from "../../Common/utils";

class Index extends React.Component {

    componentWillMount() {

        setTitle(translator('navigation_partners'))

        this.fetchItems()

        const {Country} = this.props.Partner
        if (!Country.isLoading && Country.items.length === 0) {
            this.props.dispatch(FetchCountries())
        }
    }

    fetchItems = () => {
        const {filter, page} = this.props.Partner

        this.props.dispatch(FetchItems(filter, page))
    }

    fetchItemsIfEnter = e => {
        switch (e.keyCode) {
            case 13:
                this.fetchItems()
        }
    }

    setPage = page => {
        this.props.dispatch({
            type: PAGE_CHANGED,
            payload: page
        })
    }

    changeSelect = name => e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value) || value < 0) value = null;

        this.change(name, value)
    }

    changeStringSelect = name => e => {
        let value = e.target.value

        this.change(name, value)
    }

    changeFilter = key => e => this.change(key, e.target.value)

    change = (key, value) => {
        this.props.dispatch({
            type: FILTER_CHANGED,
            payload: {
                [key]: value
            }
        })
    }

    clearFilter = () => {
        this.props.dispatch({
            type: FILTER_CLEAR,
            payload: {}
        })
    }

    render() {

        const {
            filter,
            page,
            limit,
            total,
            isLoading,
            Country,
        } = this.props.Partner

        return <div className="bgc-white bd bdrs-3 p-20 my-3">


            <div className="row">
                <div className="col">
                    <h4 className="page-title">
                        {translator('navigation_partners')}
                    </h4>
                </div>
                <div className="col text-right">

                    <Link to="/partners/new" className="btn btn-success btn-sm">
                        <i className="fa fa-plus"/>&nbsp;{translator('add')}
                    </Link>
                </div>
            </div>

            <div className="row">
                <div className="col">
                    <div className="form-inline">
                        <div className="input-group input-group-sm mr-2 mb-2">
                            <input type="text" className="form-control"
                                   name="search"
                                   value={filter.search || ''}
                                   placeholder={translator('search_placeholder')}
                                   onKeyDown={this.fetchItemsIfEnter}
                                   onChange={this.changeFilter('search')}/>
                        </div>

                        <div className="input-group input-group-sm mr-2 mb-2">
                            <select name="country" className="form-control"
                                    value={filter.country || 0}
                                    onChange={this.changeSelect('country')}>
                                <option value={0}>{translator('select_country')}</option>
                                {Country.items.map((item, i) => <option key={i} value={item.id}>{item.name}</option>)}
                            </select>
                        </div>

                        <div className="input-group input-group-sm mr-2 mb-2">
                            <select name="statuses" className="form-control"
                                    value={filter.statuses || 0}
                                    onChange={this.changeStringSelect('statuses')}>
                                <option value={0}>{translator('select_status')}</option>
                                <option value={'created'}>{translator('partner_status_created')}</option>
                                <option value={'approved'}>{translator('partner_status_approved')}</option>
                                <option value={'rejected'}>{translator('partner_status_rejected')}</option>
                            </select>
                        </div>

                        <div className="input-group input-group-sm mr-2 mb-2">
                            <select name="types" className="form-control"
                                    value={filter.types || 0}
                                    onChange={this.changeStringSelect('types')}>
                                <option value={0}>{translator('select_type')}</option>
                                <option value="recycling">{translator('order_types_recycling')}</option>
                                <option value="junk_removal">{translator('order_types_junk_removal')}</option>
                                <option value="donation">{translator('order_types_donation')}</option>
                                <option value="shredding">{translator('order_types_shredding')}</option>
                            </select>
                        </div>

                        <button className="btn btn-sm btn-primary mr-2 mb-2"
                                disabled={isLoading}
                                onClick={this.fetchItems}>
                            <i className={"fa " + (isLoading ? "fa-spin fa-circle-o-notch" : "fa-search")}/>&nbsp;{translator('search')}
                        </button>

                        <button className="btn btn-sm btn-default mr-2 mb-2"
                                disabled={isLoading}
                                onClick={this.clearFilter}>
                            <i className="fa fa-times"/>&nbsp;{translator('clear')}
                        </button>

                    </div>
                </div>
            </div>

            <div className="row">
                <div className="col">
                    {this.renderItems()}
                </div>
            </div>

            <div className="row">
                <div className="col text-left">
                    <Paginator
                        limit={limit}
                        page={page}
                        total={total}
                        onChange={this.setPage}/>
                </div>
                <div className="col text-right">
                    <div className="p-10">{translator('total')}:&nbsp;{total}</div>
                </div>
            </div>
        </div>
    }

    renderItems = () => {

        const {items, isLoading} = this.props.Partner

        if (!isLoading && items.length === 0) {
            return <div className="banner">
                <h3>{translator('no_partners_title')}</h3>
                <h4>{translator('no_partners_footer')}</h4>
            </div>
        }

        return <div className="table-responsive mb-3">
            <table className="table table-sm table-hover">
                <thead>
                <tr>
                    <th>{translator('name')}</th>
                    <th>{translator('email')}</th>
                    <th>{translator('phone')}</th>
                    <th>{translator('status')}</th>
                    <th>{translator('country')}</th>
                    <th>{translator('postal_codes')}</th>
                    <th>{translator('created_at')}</th>
                </tr>
                </thead>

                <tbody>{items.map(this.renderChild)}</tbody>
            </table>
        </div>
    }

    renderStatus = status => {

        switch (status) {
            case 'created':
                return <div className="badge badge-pill badge-light">
                    <i className="fa fa-child"/>&nbsp;{translator('partner_status_created')}
                </div>
            case 'approved':
                return <div className="badge badge-pill badge-success">
                    <i className="fa fa-thumbs-up"/>&nbsp;{translator('partner_status_approved')}
                </div>
            case 'rejected':
                return <div className="badge badge-pill badge-danger">
                    <i className="fa fa-thumbs-down"/>&nbsp;{translator('partner_status_rejected')}
                </div>
            default:
                return status
        }
    }

    renderChild = (model, key) => {
        return <tr key={key}>
            <td className="align-middle text-nowrap">
                <Link to={'/partners/' + model.id}>{model.user.name}</Link>
            </td>
            <td className="align-middle text-nowrap">{model.user.email || ''}</td>
            <td className="align-middle text-nowrap">{model.user.phone || ''}</td>
            <td className="align-middle text-nowrap">
                {this.renderStatus(model.status)}
            </td>
            <td className="align-middle text-nowrap">
                <div>{model.country ? model.country.name : ''}</div>
                <small>{model.location ? model.location.address : null}</small>
            </td>
            <td className="align-middle text-nowrap text-right">x{model.postalCodes.length}</td>
            <td className="align-middle text-nowrap">{dateFormat(model.createdAt)}</td>
        </tr>
    }
}

export default withRouter(connect(selectors)(Index))
