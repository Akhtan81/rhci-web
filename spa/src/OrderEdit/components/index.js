import React from 'react';
import moment from 'moment';
import {connect} from 'react-redux';
import {Link, Redirect, withRouter} from 'react-router-dom';
import {MODEL_CHANGED} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import DateTime from '../../Common/components/DateTime';
import {dateFormat, priceFormat, setTitle} from '../../Common/utils';
import Chat from './Chat';

const rowStyle = {width: '150px'}
const inputStyle = {width: '250px'}

class OrderEdit extends React.Component {

    state = {
        canRedirect: false,
        redirectLocation: '/orders'
    }

    componentWillMount() {

        const {id} = this.props.match.params
        if (id > 0) {

            setTitle(translator('loading'))

            this.props.dispatch(FetchItem(id, () => {
                this.setState({canRedirect: true})
            }))
        } else {
            this.setState({
                canRedirect: true
            })
        }
    }

    change = key => (value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    changeInt = name => e => {
        let value = parseInt(e.target.value.replace(/[^0-9]/g, ''))
        if (isNaN(value)) value = 0;

        this.change(name)(value)
    }

    getError = key => {
        const {errors} = this.props.OrderEdit.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    submit = () => {
        const {model} = this.props.OrderEdit

        this.props.dispatch(Save(model))
    }

    setStatus = status => () => {
        const {model} = this.props.OrderEdit

        this.props.dispatch(Save({
            id: model.id,
            status
        }))
    }

    approvePrice = () => {
        this.change('isPriceApproved')(true)
    }

    approveScheduledAt = () => {
        this.change('isScheduleApproved')(true)
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
            case 'failed':
                return <div className="badge badge-pill badge-dark">
                    <i className='fa fa-warning'/>&nbsp;{translator('order_status_failed')}
                </div>
            default:
                return status
        }
    }

    renderPaymentStatus = status => {
        switch (status) {
            case 'created':
                return <div className="badge badge-pill badge-light">
                    <i className='fa fa-clock-o'/>&nbsp;{translator('payment_status_created')}
                </div>
            case 'success':
                return <div className="badge badge-pill badge-success">
                    <i className='fa fa-thumbs-up'/>&nbsp;{translator('payment_status_success')}
                </div>
            case 'failure':
                return <div className="badge badge-pill badge-danger">
                    <i className='fa fa-thumbs-down'/>&nbsp;{translator('payment_status_failure')}
                </div>
            default:
                return status
        }
    }

    renderActions = () => {
        const {model, isLoading, isValid} = this.props.OrderEdit

        if (!model.id) return null

        const actions = []

        switch (model.status) {
            case 'created':

                actions.push(<button
                    key={0}
                    className="btn btn-outline-danger btn-sm mr-2"
                    onClick={this.setStatus('rejected')}
                    disabled={isLoading || !isValid}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-down"}/>
                    &nbsp;{translator('order_reject')}
                </button>)

                actions.push(<button
                    key={1}
                    className="btn btn-outline-success btn-sm mr-2"
                    onClick={this.setStatus('approved')}
                    disabled={isLoading || !isValid}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-up"}/>
                    &nbsp;{translator('order_approve')}
                </button>)

                break;
            case 'approved':

                actions.push(<button
                    key={0}
                    className="btn btn-warning btn-sm mr-2"
                    onClick={this.setStatus('in_progress')}
                    disabled={isLoading || !isValid}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-bolt"}/>
                    &nbsp;{translator('order_in_progress')}
                </button>)

                actions.push(<button
                    key={1}
                    className="btn btn-outline-danger btn-sm mr-2"
                    onClick={this.setStatus('canceled')}
                    disabled={isLoading || !isValid}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-ban"}/>
                    &nbsp;{translator('order_cancel')}
                </button>)

                break;
            case 'in_progress':

                actions.push(<button
                    key={0}
                    className="btn btn-primary btn-sm mr-2"
                    onClick={this.setStatus('done')}
                    disabled={isLoading || !isValid}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                    &nbsp;{translator('order_done')}
                </button>)

                actions.push(<button
                    key={1}
                    className="btn btn-outline-danger btn-sm mr-2"
                    onClick={this.setStatus('canceled')}
                    disabled={isLoading || !isValid}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-ban"}/>
                    &nbsp;{translator('order_cancel')}
                </button>)

                break;
        }

        return actions
    }

    renderItems = () => {

        const {model, isLoading} = this.props.OrderEdit

        if (isLoading) {
            return <div className="banner">
                <p><i className="fa fa-spin fa-circle-o-notch"/></p>
            </div>
        }

        if (model.items.length === 0) {
            return <div className="banner">
                <h4>{translator('order_no_items_title')}</h4>
            </div>
        }

        return <div className="table-responsive">
            <table className="table table-sm">
                <thead>
                <tr>
                    <th>{translator('id')}</th>
                    <th>{translator('category')}</th>
                    <th className="text-right">{translator('quantity')}</th>
                    <th className="text-right">{translator('price')}</th>
                </tr>
                </thead>
                <tbody>
                {model.items.map((item, i) => {
                    return <tr key={i}>
                        <td className="align-middle">{item.id}</td>
                        <td className="align-middle">{item.category.name}</td>
                        <td className="align-middle text-right">{item.quantity}</td>
                        <td className="align-middle text-right">{item.category.hasPrice ? priceFormat(item.price) : '-'}</td>
                    </tr>
                })}
                </tbody>
            </table>
        </div>
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

    renderPayments = () => {

        const {model, isLoading} = this.props.OrderEdit

        if (isLoading) {
            return <div className="banner">
                <p><i className="fa fa-spin fa-circle-o-notch"/></p>
            </div>
        }

        if (model.payments.length === 0) {
            return <div className="banner">
                <h4>{translator('order_no_payments_title')}</h4>
            </div>
        }

        return <div className="table-responsive">
            <table className="table table-sm">
                <thead>
                <tr>
                    <th className="align-middle">{translator('id')}</th>
                    <th className="align-middle">{translator('type')}</th>
                    <th className="align-middle">{translator('status')}</th>
                    <th className="align-middle text-right">{translator('price')}</th>
                    <th className="align-middle">{translator('created_at')}</th>
                </tr>
                </thead>
                <tbody>
                {model.payments.map((item, i) => {
                    return <tr key={i}>
                        <td className="align-middle">{item.id}</td>
                        <td className="align-middle">{item.type}</td>
                        <td className="align-middle">{this.renderPaymentStatus(item.status)}</td>
                        <td className="align-middle text-right">{priceFormat(item.price)}</td>
                        <td className="align-middle">{dateFormat(item.createdAt)}</td>
                    </tr>
                })}
                </tbody>
            </table>
        </div>
    }

    render() {

        if (this.state.canRedirect) {
            return <Redirect to={this.state.redirectLocation}/>
        }

        const {model, isLoading, isValid, isSaveSuccess, serverErrors} = this.props.OrderEdit

        const isEditable = model.id && ['created', 'approved'].indexOf(model.status) !== -1
        const isPriceEditable = model.id && ['in_progress'].indexOf(model.status) !== -1

        if (model.id) {
            setTitle('#' + model.id + ' | ' + model.user.name + ' | ' + model.location.address)
        }

        let address = ''
        if (model.location) {
            address = model.location.postalCode

            if (model.location.address) {
                address += ' | ' + model.location.address
            }
        }

        return <div className="bgc-white bd bdrs-3 p-20 my-3">

            <div className="row mb-3">
                <div className="col-12 col-lg-6">
                    <h4 className="page-title">
                        {translator('navigation_orders')}&nbsp;/&nbsp;
                        {!isLoading && model.id > 0
                            ? <span>#{model.id}</span>
                            : <i className="fa fa-spin fa-circle-o-notch"/>}
                    </h4>
                    <div>{model.id && this.renderStatus(model.status)}</div>
                </div>
                <div className="col-12 col-lg-6 text-right">

                    {this.renderActions()}

                    {isEditable || isPriceEditable ? <button
                        className="btn btn-success btn-sm mr-2"
                        onClick={this.submit}
                        disabled={isLoading || !isValid}>
                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                        &nbsp;{translator('save')}
                    </button> : null}

                    {isSaveSuccess && <div className="text-muted c-green-500">
                        <i className="fa fa-check"/>&nbsp;{translator('save_success_alert')}
                    </div>}
                </div>
            </div>

            <div className="row">
                <div className="col">

                    {serverErrors.length > 0 && <div className="alert alert-danger">
                        <ul className="simple">{serverErrors.map((e, i) => <li key={i}>{e}</li>)}</ul>
                    </div>}

                    {model.statusReason && <div className="alert alert-warning">
                        <p className="m-0"><i className="fa fa-warning"/>&nbsp;{model.statusReason}</p>
                    </div>}

                    <div className="row">
                        <div className="col-12 col-lg-8">
                            <table className="table table-sm mb-3">
                                <tbody>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('created_at')}</th>
                                    <td className="align-middle">{dateFormat(model.createdAt)}</td>
                                </tr>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('updated_at')}</th>
                                    <td className="align-middle">{dateFormat(model.updatedAt)}</td>
                                </tr>

                                {model.deletedAt && <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('deleted_at')}</th>
                                    <td className="align-middle">{dateFormat(model.deletedAt)}</td>
                                </tr>}

                                <tr>
                                    <th className="align-middle" style={rowStyle}>

                                        {translator('price')}

                                        {model.id ? <div>
                                            {model.isPriceApproved
                                                ? <div className="badge badge-pill badge-success">
                                                    <i className='fa fa-check'/>&nbsp;{translator('confirmed')}
                                                </div>
                                                : <div className="badge badge-pill badge-danger">
                                                    <i className='fa fa-warning'/>&nbsp;{translator('need_confirmation')}
                                                </div>}
                                        </div> : null}
                                    </th>
                                    <td className="align-middle">

                                        {isPriceEditable
                                            ? <div>
                                                <div className="input-group" style={inputStyle}>
                                                    <input type="number"
                                                           className="form-control"
                                                           min={0}
                                                           step={1}
                                                           value={model.price >= 0 ? model.price : ''}
                                                           onChange={this.changeInt('price')}/>
                                                    <div className="input-group-append">
                                                        <button className="btn btn-success"
                                                                onClick={this.approvePrice}>
                                                            <i className="fa fa-check"/>
                                                        </button>
                                                    </div>
                                                </div>

                                                <small className="text-muted d-block">
                                                    <i className="fa fa-info-circle"/>&nbsp;{translator('price_notice')}
                                                </small>
                                            </div>
                                            : priceFormat(model.price)}

                                        {this.getError('price')}
                                    </td>
                                </tr>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>
                                        {translator('scheduled_at')}

                                        {model.id ? <div>
                                            {model.isScheduleApproved
                                                ? <div className="badge badge-pill badge-success">
                                                    <i className='fa fa-check'/>&nbsp;{translator('confirmed')}
                                                </div>
                                                : <div className="badge badge-pill badge-danger">
                                                    <i className='fa fa-warning'/>&nbsp;{translator('need_confirmation')}
                                                </div>}
                                        </div> : null}
                                    </th>
                                    <td className="align-middle">

                                        {isEditable
                                            ? <div className="input-group">
                                                <DateTime
                                                    inputProps={{className: 'form-control w-100'}}
                                                    value={model.scheduledAt ? moment(model.scheduledAt) : null}
                                                    onChange={this.change('scheduledAt')}/>
                                                <div className="input-group-append">
                                                    <button className="btn btn-success"
                                                            onClick={this.approveScheduledAt}>
                                                        <i className="fa fa-check"/>
                                                    </button>
                                                </div>
                                            </div>
                                            : model.scheduledAt}

                                        <div className="help-block">
                                            <i className="fa fa-info-circle"/>&nbsp;{translator('scheduled_at_notice')}
                                        </div>

                                        {this.getError('scheduledAt')}
                                    </td>
                                </tr>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('user')}</th>
                                    <td className="align-middle">
                                        <div>{model.user ? model.user.name : null}</div>
                                        {model.user && model.user.email ?
                                            <div><i className="fa fa-at"/>&nbsp;{model.user.email}</div> : null}
                                        {model.user && model.user.phone ?
                                            <div><i className="fa fa-phone"/>&nbsp;{model.user.phone}</div> : null}
                                    </td>
                                </tr>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('partner')}</th>
                                    <td className="align-middle">
                                        {model.partner
                                            ? <Link to={"/partners/" + model.partner.id}>
                                                {model.partner.user.name}
                                            </Link>
                                            : null}
                                    </td>
                                </tr>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('repeatable')}</th>
                                    <td className="align-middle">
                                        {!model.repeatable ? translator('repeatable_none') : null}
                                        {model.repeatable === 'week' ? translator('repeatable_week') : null}
                                        {model.repeatable === 'month' ? translator('repeatable_month') : null}
                                        {model.repeatable === 'month-3' ? translator('repeatable_month_3') : null}
                                    </td>
                                </tr>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('type')}</th>
                                    <td className="align-middle">{this.renderType(model.type)}</td>
                                </tr>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('location')}</th>
                                    <td className="align-middle">
                                        <div>{address}</div>

                                        {model.location &&
                                        <a href={`https://www.google.com/maps/@${model.location.lng},${model.location.lat},15z`}
                                           target="_blank">
                                            <i className="fa fa-map-marker"/>&nbsp;{translator('show_on_map')}
                                        </a>}
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <h4>{translator('order_items')}</h4>
                            {this.renderItems()}

                            <h4>{translator('order_payments')}</h4>
                            {this.renderPayments()}

                        </div>
                        <div className="col-12 col-lg-4">
                            <Chat/>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(OrderEdit))
