import React from 'react';
import moment from 'moment';
import {connect} from 'react-redux';
import {Link, Redirect, withRouter} from 'react-router-dom';
import {MODEL_CHANGED, TOGGLE_GALLERY, SET_GALLERY_IMAGE} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import DateTime from '../../Common/components/DateTime';
import {dateFormat, priceFormat, setTitle} from '../../Common/utils';
import Media from './Media';
import Lightbox from 'react-images';
import {renderStatus, renderType} from "../../Order/utils";

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

    changePrice = e => {
        let value = parseFloat(e.target.value.replace(/[^0-9.]/g, ''))
        if (isNaN(value)) value = 0;

        this.change('price')(value)
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
            ...model,
            status
        }))
    }

    approvePrice = () => {
        const {model} = this.props.OrderEdit

        this.props.dispatch(Save({
            ...model,
            isPriceApproved: true
        }))
    }

    approveScheduledAt = () => {
        const {model} = this.props.OrderEdit

        this.props.dispatch(Save({
            ...model,
            isScheduleApproved: true
        }))
    }

    toggleGallery = () => {
        this.props.dispatch({
            type: TOGGLE_GALLERY,
        })
    }

    setGalleryImage = index => () => {
        const {images} = this.props.OrderEdit.Gallery

        let payload = index
        if (payload > images.length - 1) {
            payload = 0;
        } else if (payload < 0) {
            payload = images.length - 1
        }

        this.props.dispatch({
            type: SET_GALLERY_IMAGE,
            payload
        })
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
                    <th style={{width: '50px'}}>{translator('id')}</th>
                    <th style={{width: '150px'}}>{translator('category')}</th>
                    <th style={{width: '100px'}} className="text-right">{translator('quantity')}</th>
                    <th style={{width: '100px'}} className="text-right">{translator('price')}</th>
                    <th>{translator('order_item_message')}</th>
                </tr>
                </thead>
                <tbody>
                {model.items.map((item, i) => {
                    return <tr key={i}>
                        <td>{item.id}</td>
                        <td>
                            <div>{item.category.name}</div>
                        </td>
                        <td className="text-right text-nowrap">{item.quantity}</td>
                        <td className="text-right text-nowrap">{item.category.hasPrice ? priceFormat(item.quantity * item.price) : '-'}</td>
                        <td>
                            {item.message ? <div>
                                {item.message.text && <div className="mb-3">{item.message.text}</div>}

                                {item.message.media && item.message.media.length > 0
                                    ? <div className="row no-gutters">{item.message.media.map((item, i) =>
                                        <Media key={i} media={item}/>
                                    )}</div>
                                    : null}
                            </div> : null}
                        </td>
                    </tr>
                })}
                </tbody>
            </table>
        </div>
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
        const {Gallery} = this.props.OrderEdit

        const isEditable = model.id && ['created', 'approved'].indexOf(model.status) !== -1
        const isPriceEditable = model.id && ['in_progress'].indexOf(model.status) !== -1

        if (model.id) {
            setTitle('#' + model.id
                + ' | ' + model.location.address
                + ' | ' + (model.user.name || model.user.email || model.user.phone || '-'))
        }

        let address = ''
        if (model.location) {
            address = model.location.postalCode

            if (model.location.address) {
                address += ' | ' + model.location.address
            }
        }

        let displayedPrice = model.price
        if (model.type === 'recycling' && parseFloat(displayedPrice) === 0) {
            displayedPrice = translator('not_available')
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
                    <div>{model.id && renderStatus(model.status)}</div>
                </div>
                <div className="col-12 col-lg-6 text-right">

                    {this.renderActions()}

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
                        <div className="col-12">
                            <table className="table table-sm mb-3">
                                <tbody>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('created_at')}</th>
                                    <td className="align-middle">{dateFormat(model.createdAt)}</td>
                                </tr>

                                {model.deletedAt && <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('deleted_at')}</th>
                                    <td className="align-middle">{dateFormat(model.deletedAt)}</td>
                                </tr>}

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
                                        {this.props.isAdmin
                                            ? (model.partner ? <Link to={"/partners/" + model.partner.id}>
                                                {model.partner.user.name}
                                            </Link> : null)
                                            : (model.partner ? model.partner.user.name : null)}
                                    </td>
                                </tr>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('type')}</th>
                                    <td className="align-middle">{renderType(model.type)}</td>
                                </tr>

                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('price')}</th>
                                    <td className="align-middle">

                                        {isPriceEditable
                                            ? <div>
                                                <div className="input-group" style={inputStyle}>
                                                    <input type="number"
                                                           className="form-control"
                                                           min={0}
                                                           step={1}
                                                           value={model.price !== null ? model.price : ''}
                                                           onChange={this.changePrice}/>
                                                    <div className="input-group-append">
                                                        <button className="btn btn-success"
                                                                disabled={!isValid || isLoading}
                                                                onClick={this.approvePrice}>
                                                            <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                                                        </button>
                                                    </div>
                                                </div>

                                            </div>
                                            : displayedPrice}

                                        {this.getError('price')}
                                    </td>
                                </tr>
                                <tr>
                                    <th className="align-middle" style={rowStyle}>{translator('scheduled_at')}</th>
                                    <td className="align-middle">

                                        {isEditable
                                            ? <div className="input-group">
                                                <DateTime
                                                    inputProps={{className: 'form-control w-100'}}
                                                    value={model.scheduledAt ? moment(model.scheduledAt) : null}
                                                    onChange={this.change('scheduledAt')}/>
                                                <div className="input-group-append">
                                                    <button className="btn btn-success"
                                                            disabled={!isValid || isLoading}
                                                            onClick={this.approveScheduledAt}>
                                                        <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                                                    </button>
                                                </div>
                                            </div>
                                            : model.scheduledAt}

                                        {this.getError('scheduledAt')}
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
                                {model.message ? <tr>
                                    <th style={rowStyle}>{translator('order_message')}</th>
                                    <td>
                                        {model.message.text && <div className="mb-3">{model.message.text}</div>}

                                        {model.message.media && model.message.media.length > 0
                                            ? <div className="row no-gutters">{model.message.media.map((item, i) =>
                                                <Media key={i} media={item}/>
                                            )}</div>
                                            : null}
                                    </td>
                                </tr> : null}
                                </tbody>
                            </table>
                        </div>

                        <div className="col-12">
                            <h4>{translator('order_items')}</h4>
                            {this.renderItems()}
                        </div>

                        <div className="col-12">
                            <h4>{translator('order_payments')}</h4>
                            {this.renderPayments()}

                        </div>
                    </div>

                </div>
            </div>

            <Lightbox
              images={Gallery.images}
              isOpen={Gallery.isOpen}
              currentImage={Gallery.currentImage}
              onClickPrev={this.setGalleryImage(Gallery.currentImage - 1)}
              onClickNext={this.setGalleryImage(Gallery.currentImage + 1)}
              onClose={this.toggleGallery}
            />
        </div>
    }
}

export default withRouter(connect(selectors)(OrderEdit))
