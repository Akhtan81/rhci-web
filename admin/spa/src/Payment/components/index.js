import React from 'react';
import {connect} from 'react-redux';
import {withRouter, Link} from 'react-router-dom';
import selectors from './selectors';
import translator from '../../translations/translator';
import Paginator from '../../Common/components/Paginator';
import {FETCH_REQUEST, FILTER_CHANGED, FILTER_CLEAR, PAGE_CHANGED} from '../actions';
import {dateFormat, priceFormat, setTitle} from "../../Common/utils";
import {renderPaymentStatus} from "../utils";
import Save from "../actions/Save";

class Index extends React.Component {

    componentWillMount() {

        setTitle(translator('navigation_payments'))

        this.setPage(1)
    }

    save = (id, status) => () => {

        if (!confirm(translator('confirm_payment_action'))) return

        this.props.dispatch(Save({
            id, status
        }))
    }

    fetchItems = () => {
        this.props.dispatch({
            type: FETCH_REQUEST
        })
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
        } = this.props.Payment

        return <div className="card my-3">

            <div className="card-header">
                <h4 className="m-0">
                    {translator('navigation_payments')}
                </h4>
            </div>

            <div className="card-body">
                <div className="row">
                    <div className="col">
                        <div className="form-inline">

                            <div className="input-group input-group-sm mr-2 mb-2">
                                <select name="statuses" className="form-control"
                                        value={filter.statuses || 0}
                                        onChange={this.changeStringSelect('statuses')}>
                                    <option value={0}>{translator('select_status')}</option>
                                    <option value={'created'}>{translator('payment_status_created')}</option>
                                    <option value={'success'}>{translator('payment_status_success')}</option>
                                    <option value={'failure'}>{translator('payment_status_failure')}</option>
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

    renderItems = () => {

        const {items, isLoading} = this.props.Payment

        if (!isLoading && items.length === 0) {
            return <div className="banner">
                <h3>{translator('no_payments_title')}</h3>
                <h4>{translator('no_payments_footer')}</h4>
            </div>
        }

        return <div className="table-responsive mb-3">
            <table className="table table-sm table-hover">
                <thead>
                <tr>
                    <th className="align-middle">{translator('status')}</th>
                    <th className="align-middle">{translator('order')}</th>
                    <th className="align-middle">{translator('id')}</th>
                    <th className="align-middle">{translator('type')}</th>
                    <th className="align-middle text-right">{translator('price')}</th>
                    <th className="align-middle">{translator('created_at')}</th>
                    <th className="align-middle">{translator('updated_at')}</th>
                </tr>
                </thead>

                <tbody>{items.map(this.renderChild)}</tbody>
            </table>
        </div>
    }

    renderChild = (model, key) => {

        const {isUpdating, isLoading} = this.props.Payment

        return <tr key={key}>
            <td className="align-middle">
                {model.status === 'created' ?
                    <button className="btn btn-sm btn-outline-danger mr-1"
                            disabled={isUpdating || isLoading}
                            onClick={this.save(model.id, 'failure')}>
                        <i className="fa fa-thumbs-down"/>&nbsp;{translator('reject')}
                    </button> : null}

                {model.status === 'created' ?
                    <button className="btn btn-sm btn-outline-success"
                            disabled={isUpdating || isLoading}
                            onClick={this.save(model.id, 'success')}>
                        <i className="fa fa-thumbs-up"/>&nbsp;{translator('approve')}
                    </button> : null}

                {model.status !== 'created' ?
                    renderPaymentStatus(model.status) : null}
            </td>
            <td className="align-middle">
                <Link to={'/orders/' + model.order.id}>{translator('order')} #{model.order.id}</Link>
            </td>
            <td className="align-middle">{model.id}</td>
            <td className="align-middle">{model.type}</td>
            <td className="align-middle text-right">{priceFormat(model.price)}, {model.currency}</td>
            <td className="align-middle">{dateFormat(model.createdAt)}</td>
            <td className="align-middle">{dateFormat(model.updatedAt)}</td>
        </tr>
    }
}

export default withRouter(connect(selectors)(Index))
