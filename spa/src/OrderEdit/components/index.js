import React from 'react';
import {connect} from 'react-redux';
import {withRouter} from 'react-router-dom';
import {MODEL_CHANGED, FETCH_SUCCESS} from '../actions';
import selectors from './selectors';
import Save from '../actions/Save';
import FetchItem from '../actions/FetchItem';
import translator from '../../translations/translator';

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

        const {model, isValid, isLoading, isSaveSuccess, serverErrors} = this.props.OrderEdit

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
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    }
}

export default withRouter(connect(selectors)(OrderEdit))
