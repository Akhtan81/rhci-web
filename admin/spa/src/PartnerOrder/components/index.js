import React from 'react';
import {connect} from 'react-redux';
import {Link, withRouter} from 'react-router-dom';
import selectors from './selectors';
import translator from '../../translations/translator';
import FetchItems from '../actions/FetchItems';
import Paginator from '../../Common/components/Paginator';
import {FILTER_CHANGED, FILTER_CLEAR, PAGE_CHANGED} from '../actions';
import {dateFormat, priceFormat, setTitle} from "../../Common/utils";
import {renderStatus, renderType} from "../../Order/utils";

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
                address += ' | ' + model.location.address
            }
        }

        address = address.substr(0, 50) + '...'

        return <tr key={key}>
            <td className="text-nowrap align-middle">
                <Link to={'/orders/' + model.id} className="btn btn-sm btn-success">{model.id}</Link>
            </td>
            <td className="text-nowrap align-middle">
                <div>{(model.user.name || model.user.phone || model.user.email || '-')}</div>
            </td>
            <td className="text-nowrap align-middle">{renderStatus(model.status)}</td>
            <td className="text-nowrap align-middle">{model.type ? renderType(model.type.key) : '-'}</td>
            <td className="text-nowrap align-middle text-right">
                <div>{priceFormat(model.price)}</div>
                {model.items && <small className="text-muted">x{model.items.length}</small>}
            </td>
            <td className="text-nowrap align-middle">{address}</td>
            <td className="text-nowrap align-middle">{dateFormat(model.scheduledAt)}</td>
            <td className="text-nowrap align-middle">{dateFormat(model.createdAt)}</td>
        </tr>
    }

    render() {

        const {
            filter,
            page,
            limit,
            total,
            isLoading,
        } = this.props.Order

        const {
            partner
        } = this.props.User

        let hasSubscription = true;

        if (partner && partner.subscription) {
            const subscription = partner.subscription

            if (subscription.id) {
                hasSubscription = subscription.status === 'active';
            }
        }

        return <div className="card my-3">

            <div className="card-header">
                <h4 className="m-0">{translator('navigation_orders')}</h4>
            </div>

            <div className="card-body">

                {!hasSubscription && <div className="row">
                    <div className="col">
                        <div className="alert alert-warning">
                            <i className="fa fa-warning"/>{translator('partner_no_active_subscription_warning')}
                        </div>
                    </div>
                </div>}

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
                                    <option value="donation">{translator('order_types_donation')}</option>
                                    <option disabled={true}
                                            value="shredding">{translator('order_types_shredding')}</option>
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
        </div>
    }

}

export default withRouter(connect(selectors)(Index))
