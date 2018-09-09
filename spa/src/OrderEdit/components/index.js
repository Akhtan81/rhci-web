import React from 'react';
import {connect} from 'react-redux';
import {withRouter, Link} from 'react-router-dom';
import {MODEL_CHANGED, FETCH_SUCCESS} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';
import GoogleMap from '../../Common/components/GoogleMap';
import {numberFormat} from '../../Common/utils';

class OrderEdit extends React.Component {

    componentWillMount() {

        const {id} = this.props.match.params
        if (id > 0) {
            this.props.dispatch(FetchItem(id))
        } else {
            this.props.dispatch({
                type: FETCH_SUCCESS,
                payload: {}
            })
        }
    }

    change = (key, value = null) => this.props.dispatch({
        type: MODEL_CHANGED,
        payload: {
            [key]: value
        }
    })

    getError = key => {
        const {errors} = this.props.OrderEdit.validator

        if (errors[key] === undefined) return null

        return <small className="d-block c-red-500 form-text text-muted">{errors[key]}</small>
    }

    setStatus = status => () => {
        const {model} = this.props.OrderEdit

        this.props.dispatch(Save({
            id: model.id,
            status
        }))
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

    renderActions = () => {
        const {model, isLoading} = this.props.OrderEdit

        if (!model.id) return null

        const actions = []

        switch (model.status) {
            case 'created':

                actions.push(<button
                    key={0}
                    className="btn btn-outline-danger btn-sm mr-2"
                    onClick={this.setStatus('rejected')}
                    disabled={isLoading}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-down"}/>
                    &nbsp;{translator('order_reject')}
                </button>)

                actions.push(<button
                    key={1}
                    className="btn btn-success btn-sm mr-2"
                    onClick={this.setStatus('approved')}
                    disabled={isLoading}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-thumbs-up"}/>
                    &nbsp;{translator('order_approve')}
                </button>)

                break;
            case 'approved':

                actions.push(<button
                    key={0}
                    className="btn btn-warning btn-sm mr-2"
                    onClick={this.setStatus('in_progress')}
                    disabled={isLoading}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-bolt"}/>
                    &nbsp;{translator('order_in_progress')}
                </button>)

                actions.push(<button
                    key={1}
                    className="btn btn-outline-danger btn-sm mr-2"
                    onClick={this.setStatus('canceled')}
                    disabled={isLoading}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-ban"}/>
                    &nbsp;{translator('order_cancel')}
                </button>)

                break;
            case 'in_progress':

                actions.push(<button
                    key={0}
                    className="btn btn-primary btn-sm mr-2"
                    onClick={this.setStatus('done')}
                    disabled={isLoading}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-check"}/>
                    &nbsp;{translator('order_done')}
                </button>)

                actions.push(<button
                    key={1}
                    className="btn btn-outline-danger btn-sm mr-2"
                    onClick={this.setStatus('canceled')}
                    disabled={isLoading}>
                    <i className={isLoading ? "fa fa-spin fa-circle-o-notch" : "fa fa-ban"}/>
                    &nbsp;{translator('order_cancel')}
                </button>)

                break;
        }

        return actions
    }

    render() {

        const {model, isLoading, isSaveSuccess, serverErrors} = this.props.OrderEdit

        return <div className="bgc-white bd bdrs-3 p-20 mB-20">

            <div className="row mb-3">
                <div className="col-12 col-lg-8">
                    <h4 className="page-title">
                        {translator('navigation_orders')}&nbsp;/&nbsp;
                        {!isLoading && model.id > 0
                            ? <span>#{model.id}</span>
                            : <i className="fa fa-spin fa-circle-o-notch"/>}
                    </h4>
                    <div>{model.id && this.renderStatus(model.status)}</div>
                </div>
                <div className="col-12 col-lg-4 text-right">

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

                    <div className="row">
                        <div className="col-12 col-lg-8">
                            <div className="table-responsive">
                                <table className="table table-sm">
                                    <tbody>
                                    <tr>
                                        <th className="align-middle">{translator('created_at')}</th>
                                        <td className="align-middle">{model.createdAt}</td>
                                    </tr>
                                    <tr>
                                        <th className="align-middle">{translator('updated_at')}</th>
                                        <td className="align-middle">{model.updatedAt}</td>
                                    </tr>
                                    <tr>
                                        <th className="align-middle">{translator('price')}</th>
                                        <td className="align-middle">{model.price ? numberFormat(model.price) : null}</td>
                                    </tr>
                                    <tr>
                                        <th className="align-middle">{translator('user')}</th>
                                        <td className="align-middle">
                                            <div>{model.user ? model.user.name : null}</div>
                                            <div>{model.user && model.user.email ? model.user.email : null}</div>
                                            <div>{model.user && model.user.phone ? model.user.phone : null}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th className="align-middle">{translator('partner')}</th>
                                        <td className="align-middle">{model.partner
                                            ?
                                            <Link to={"/partners/" + model.partner.id}>{model.partner.user.name}</Link>
                                            : null}</td>
                                    </tr>
                                    <tr>
                                        <th className="align-middle">{translator('district')}</th>
                                        <td className="align-middle">{model.district
                                            ? model.district.postalCode + " | " + model.district.fullName
                                            : null}</td>
                                    </tr>
                                    <tr>
                                        <th className="align-middle">{translator('scheduled_at')}</th>
                                        <td className="align-middle">
                                            {model.scheduledAt}&nbsp;

                                            {model.isScheduleConfirmed
                                                ? <div className="badge badge-pill badge-success">
                                                    <i className='fa fa-ban'/>&nbsp;{translator('confirmed')}
                                                </div>
                                                : <div className="badge badge-pill badge-danger">
                                                    <i className='fa fa-ban'/>&nbsp;{translator('need_confirmation')}
                                                </div>}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th className="align-middle">{translator('repeatable')}</th>
                                        <td className="align-middle">
                                            {!model.repeatable ? translator('repeatable_none') : null}
                                            {model.repeatable === 'week' ? translator('repeatable_week') : null}
                                            {model.repeatable === 'month' ? translator('repeatable_month') : null}
                                            {model.repeatable === 'month-3' ? translator('repeatable_month_3') : null}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th className="align-middle">{translator('location')}</th>
                                        <td className="align-middle">{model.location ? model.location.address : null}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                            <h4>{translator('order_items')}</h4>
                            <div className="table-responsive">
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
                                            <td className="align-middle text-right">{numberFormat(item.price)}</td>
                                        </tr>
                                    })}
                                    </tbody>
                                </table>
                            </div>

                            <div className="bd bgc-white mb-3">
                                <div className="layers">
                                    <div className="layer w-100 p-20"><h6
                                        className="lh-1">{translator('order_messages')}</h6></div>
                                    <div className="layer w-100">
                                        <div className="bgc-grey-200 p-20 gapY-15">
                                            {model.messages.map((item, i) => {

                                                return <div key={i} className="peers fxw-nw">
                                                    <div className="peer mR-20">
                                                        <img className="w-2r bdrs-50p" src={item.user.avatar.url}/>
                                                    </div>
                                                    <div className="peer peer-greed">
                                                        <div className="layers ai-fs gapY-5">
                                                            <div className="layer">
                                                                <div
                                                                    className="peers ai-c pY-3 pX-10 bgc-white bdrs-2 lh-3/2">
                                                                    <div className="peer mR-10 w-100">
                                                                        <small>{item.user.name}</small>
                                                                    </div>
                                                                    <div className="peer-greed w-100">
                                                                        <span>{item.text}</span>
                                                                    </div>
                                                                    <div className="peer mR-10 w-100">
                                                                        <small>{item.createdAt}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            })}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="col-12 col-lg-4">
                            {model.location && <GoogleMap
                                lat={model.location.lat}
                                lng={model.location.lng}/>}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(OrderEdit))
