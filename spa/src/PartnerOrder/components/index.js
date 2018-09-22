import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import selectors from './selectors';
import translator from '../../translations/translator';
import FetchItems from '../actions/FetchItems';
import Paginator from '../../Common/components/Paginator';
import {FILTER_CHANGED, FILTER_CLEAR, PAGE_CHANGED} from '../actions';
import {dateFormat, priceFormat, setTitle} from "../../Common/utils";

class Index extends React.Component {

    state = {
        intervalId: null
    }

    componentWillMount() {

        setTitle(translator('navigation_orders'))

        this.fetchItems()
    }

    componentDidMount() {
        const intervalId = setInterval(this.fetchItems, 30000);

        this.setState({intervalId: intervalId});
    }

    componentWillUnmount() {
        clearInterval(this.state.intervalId);
    }

    fetchItems = () => {
        const {filter, page} = this.props.Order

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
        let value = !e.target.value ? null : e.target.value

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


    renderItems = () => {

        const {items, isLoading} = this.props.Order

        if (!isLoading && items.length === 0) {
            return <div className="banner">
                <h3>{translator('no_orders_title')}</h3>
                <h4>{translator('no_orders_footer')}</h4>
            </div>
        }

        if (isLoading && items.length === 0) return null

        return <div className="table-responsive mb-3">
            <table className="table table-sm table-hover">
                <thead>
                <tr>
                    <th className="text-nowrap">{translator('id')}</th>
                    <th className="text-nowrap">{translator('user')}</th>
                    <th className="text-nowrap">{translator('status')}</th>
                    <th className="text-nowrap">{translator('type')}</th>
                    <th className="text-nowrap">{translator('price')}</th>
                    <th className="text-nowrap">{translator('location')}</th>
                    <th className="text-nowrap">{translator('scheduled_at')}</th>
                    <th className="text-nowrap">{translator('created_at')}</th>
                </tr>
                </thead>

                <tbody>{items.map(this.renderChild)}</tbody>
            </table>
        </div>
    }

    renderChild = (model, key) => {

        let address = ''
        if (model.location) {
            address = model.location.postalCode

            if (model.location.address) {
                address += ' | ' + model.location.address.substr(0, 50) + '...'
            }
        }

        return <tr key={key}>
            <td className="text-nowrap align-middle">
                <Link to={'/orders/' + model.id} className="btn btn-sm btn-success">{model.id}</Link>
            </td>
            <td className="text-nowrap align-middle">
                <div>{model.user.name}</div>
            </td>
            <td className="text-nowrap align-middle">{this.renderStatus(model.status)}</td>
            <td className="text-nowrap align-middle">{this.renderType(model.type)}</td>
            <td className="text-nowrap align-middle text-right">
                <div>{priceFormat(model.price)}</div>
                {model.items && <small className="text-muted">x{model.items.length}</small>}
            </td>
            <td className="text-nowrap align-middle">{address}</td>
            <td className="text-nowrap align-middle">{dateFormat(model.scheduledAt)}</td>
            <td className="text-nowrap align-middle">{dateFormat(model.createdAt)}</td>
        </tr>
    }

    renderStatus = status => {
        switch (status) {
            case 'created':
                return <div className="badge badge-pill badge-light">
                    {translator('order_status_created')}
                </div>
            case 'approved':
                return <div className="badge badge-pill badge-success">
                    <i className='fa fa-thumbs-up'/>&nbsp;{translator('order_status_approved')}
                </div>
            case 'rejected':
                return <div className="badge badge-pill badge-danger">
                    <i className='fa fa-times'/>&nbsp;{translator('order_status_rejected')}
                </div>
            case 'in_progress':
                return <div className="badge badge-pill badge-warning">
                    <i className='fa fa-bolt'/>&nbsp;{translator('order_status_in_progress')}
                </div>
            case 'done':
                return <div className="badge badge-pill badge-primary">
                    <i className='fa fa-check'/>&nbsp;{translator('order_status_done')}
                </div>
            case 'canceled':
                return <div className="badge badge-pill badge-dark">
                    <i className='fa fa-ban'/>&nbsp;{translator('order_status_canceled')}
                </div>
            default:
                return status
        }
    }

    renderType = status => {

        switch (status) {
            case 'recycling':
                return <div className="badge badge-pill badge-success">
                    <i className="fa fa-recycle"/>&nbsp;{translator('order_types_recycling')}
                </div>
            case 'junk_removal':
                return <div className="badge badge-pill badge-warning">
                    <i className="fa fa-cubes"/>&nbsp;{translator('order_types_junk_removal')}
                </div>
            case 'shredding':
                return <div className="badge badge-pill badge-primary">
                    <i className="fa fa-stack-overflow"/>&nbsp;{translator('order_types_shredding')}
                </div>
            default:
                return status
        }
    }

    render() {

        const {
            filter,
            page,
            limit,
            total,
            isLoading,
        } = this.props.Order

        return <div className="bgc-white bd bdrs-3 p-20 my-3">


            <div className="row">
                <div className="col">
                    <h4 className="page-title">
                        {translator('navigation_orders')}
                    </h4>
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
                            <select name="status" className="form-control"
                                    onChange={this.changeSelect('status')}
                                    value={filter.status || 0}>
                                <option value={0}>{translator('select_status')}</option>
                                <option value="created">{translator('order_status_created')}</option>
                                <option value="approved">{translator('order_status_approved')}</option>
                                <option value="rejected">{translator('order_status_rejected')}</option>
                                <option value="in_progress">{translator('order_status_in_progress')}</option>
                                <option value="done">{translator('order_status_done')}</option>
                                <option value="canceled">{translator('order_status_canceled')}</option>
                            </select>
                        </div>


                        <div className="input-group input-group-sm mr-2 mb-2">
                            <select name="type" className="form-control"
                                    onChange={this.changeSelect('type')}
                                    value={filter.type || 0}>
                                <option value={0}>{translator('select_type')}</option>
                                <option value="recycling">{translator('order_types_recycling')}</option>
                                <option value="junk_removal">{translator('order_types_junk_removal')}</option>
                                <option disabled={true} value="shredding">{translator('order_types_shredding')}</option>
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
                <div className="col text-center">
                    <Paginator
                        limit={limit}
                        page={page}
                        total={total}
                        onChange={this.setPage}/>
                </div>
            </div>
        </div>
    }

}

export default withRouter(connect(selectors)(Index))
